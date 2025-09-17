<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Posts;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\PostShare;
use App\Models\Follow;
use App\Models\PostMedia;
use App\Models\User;
use App\Models\PostReports;
use App\Models\PostBlock;
use App\Models\AppNotification;
use App\Models\HelperLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use DB;
class PostApiController extends Controller
{

  public function feedPage(Request $request)
{
    $user = auth()->user();
    $perPage =  $request->per_page??10; // You can change this as needed
    $searchText =  $request->searchText??''; // You can change this as needed

    if (!empty($request->type) && $request->type == 'Favorites') {
        $post_ids = PostLike::where('user_id', $user->id)->pluck('post_id');
        $posts = Posts::whereIn('id', $post_ids)
                    ->with('media', 'user', 'likes', 'comments','user.countryDetail','user.nativeLanguageDetail', 'user.learningLanguageDetail', 'user.knowLanguageDetail')
                     ->when(!empty($searchText), function ($query) use ($searchText) {
                            $query->where(function ($q) use ($searchText) {
                                $q->where('caption', 'like', '%' . $searchText . '%')
                                ->orWhere('content', 'like', '%' . $searchText . '%');
                            });
                        })
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    } else {
        // Get all users this user follows
        // $followingIds = Follow::where('follower_id', $user->id)->pluck('following_id');

        // Paginated posts from followed users
        $posts = Posts::with('media', 'user', 'likes', 'comments' ,'user.countryDetail',
        'user.nativeLanguageDetail',
        'user.learningLanguageDetail',
        'user.knowLanguageDetail')
                        ->when(!empty($searchText), function ($query) use ($searchText) {
                            $query->where(function ($q) use ($searchText) {
                                $q->where('caption', 'like', '%' . $searchText . '%')
                                ->orWhere('content', 'like', '%' . $searchText . '%');
                            });
                        })
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    // Format posts
    $formattedPosts = $posts->getCollection()->map(function ($post) use ($user) {
        $media_urls = [];

        if ($post->post_type === 'photo' || $post->post_type === 'video') {
            if ($post->media_path) {
                $media_urls[] = Storage::disk('s3')->url($post->media_path);
            }
        } elseif ($post->post_type === 'carousel') {
            $media_urls = $post->media->map(function ($media) {
                return Storage::disk('s3')->url($media->media_path);
            });
        }

            if (isset($post->user->avatar)) {
               $post->user->avatar = Storage::disk('s3')->url('').$post->user->avatar;
            }
        return [
            'id' => $post->id,
            'user' => $post->user,
            'post_type' => $post->post_type,
            'content' => $post->content,
            'caption' => $post->caption,
            'media' => $media_urls,
            'like_count' => $post->likes->count(),
            'comment_count' => $post->comments->count(),
            'is_liked_by_user' => $post->likes->contains('user_id', $user->id),
            'created_at' => $post->created_at->toDateTimeString(),
        ];
    });

    return response()->json([
        'message' => 'Feed fetched successfully',
        'status' => true,
        'base_url'=>Storage::disk('s3')->url(''),
        'data' => [
            'posts' => $formattedPosts,
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
            'has_more' => $posts->currentPage() < $posts->lastPage()
        ]
    ], 200);
}


    public function index(Request $request)
        {
            if(!empty($request->post_type)){
                $posts = Posts::with('media', 'user')->where('post_type',$request->post_type)->where('user_id', $request->user_id)->latest()->get();
            } else {
                $posts = Posts::with('media', 'user')->where('user_id',  $request->user_id)->latest()->get();
            }
          
            // Return formatted response
            $data = $posts->map(function ($post) {
                $media_urls = [];

                // Handle media based on post_type
                if ($post->post_type === 'photo' || $post->post_type === 'video') {
                    if ($post->media_path) {
                        $media_urls[] = Storage::disk('s3')->url($post->media_path);
                    }
                } elseif ($post->post_type === 'carousel') {
                    $media_urls = $post->media->map(function ($media) {
                        return Storage::disk('s3')->url($media->media_path);
                    });
                }

                return [
                    'id' => $post->id,
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                        'email' => $post->user->email,
                    ],
                    'post_type' => $post->post_type,
                    'caption' => $post->caption,
                    'content' => $post->content,
                    'media' => $media_urls,
                    'created_at' => $post->created_at->toDateTimeString(),
                ];
            });

            return response()->json([
                'success' => true,
                'posts' => $data
            ],200);
        }

    public function store(Request $request)
        {
            try{
                $request->validate([
                    'post_type' => 'required|in:text,photo,video,carousel'
                ]);

                if ($request->post_type == 'text' AND empty($request->content)) {
                    return response()->json([
                            'status' => false,
                            'error'  => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_content_required') ?? 'Please enter some text — content is required.',
                        ], 500);
                } 

                if ($request->post_type == 'photo' OR $request->post_type == 'video' OR $request->post_type == 'carousel') {
                    if (empty($request->hasFile('media'))) {

                        return response()->json([
                            'status' => false,
                            'error'  => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_media_required') ?? 'Please select some media — media is required.',
                        ], 500);
                    }
                }

                $post = Posts::create([
                    'user_id' => $request->user_id,
                    'post_type' => $request->post_type,
                    'content' => $request->content??'',
                    'caption' => $request->caption??'',
                    'location' => $request->location??'',

                ]);

                if ($request->post_type === 'photo' || $request->post_type === 'video') {
                    if ($request->hasFile('media')) {
                        $mediaPath = $request->file('media')->store('posts', 's3');
                        $post->update(['media_path' => $mediaPath]);
                    }
                }

                if ($request->post_type === 'carousel') {
                    if ($request->hasFile('media')) {
                        foreach ($request->file('media') as $file) {
                            $mediaPath = $file->store('posts/carousel', 's3');
                            PostMedia::create([
                                'post_id' => $post->id,
                                'media_path' => $mediaPath
                            ]);
                        }
                    }
                }

                $followingIds = Follow::where('follower_id', $request->user_id)
                      ->pluck('following_id')
                      ->toArray();

                $users = User::whereIn('id', $followingIds)->get();
                $notifications = [];
                    foreach ($users as $user) {
                        $language_code = language_code($user->interface_language);
                        $title = HelperLanguage::retrieve_message_from_arb_file($language_code, 'web_new_post_title')
                            ?? 'New Post Alert';
                        $msg   = HelperLanguage::retrieve_message_from_arb_file($language_code, 'web_new_post_msg')
                            ?? 'Stay updated! A new post has been created by your favorite user ✨';

                        $notifications[] = [
                            'user_id'    => $user->id,
                            'type'       => 'in_app',
                            'title'      => $title,
                            'body'       => $msg,
                            'channel'    => 'in_app',
                            'data'       => $post->id,
                        ];
                    }

                    // bulk insert in one query
                    if (!empty($notifications)) {
                        AppNotification::insert($notifications);
                    }

                return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_created_successfully') ??'Post created successfully.','status'=>true],200);
            } catch(\Exception $e)  {
                 return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }

    public function update(Request $request)
        {
            try {
            $post = Posts::findOrFail($request->post_id);
            if ($post->user_id != auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->update([
                'content' => $request->content ?? $post->content,
                'caption' => $request->caption ?? $post->caption,
                'location' => $request->location ?? $post->location,
            ]);

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_updated_successfully') ?? 'Post updated successfully','status'=>true, 'post' => $post],200);
            } catch(\Exception $e)  {
                 return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }

    public function destroy(Request $request)
        {
            try {
                $post = Posts::with('media')->findOrFail($request->post_id);
               
                // Delete media files
                if ($post->post_type == 'carousel') {
                    foreach ($post->media as $media) {
                        Storage::disk('s3')->delete($media->media_path);
                        $media->delete();
                    }
                } else {
                    if(!empty($post->media_path)){
                        Storage::disk('s3')->delete($post->media_path);
                      
                    }
                }

                $post->delete();
                AppNotification::where('data', $request->post_id)->delete();
                return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_deleted_successfully') ?? 'Post deleted successfully','status'=>true],200);
            } catch(\Exception $e)  {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }

    public function like(Request $request)
        {
            try {
            $like = PostLike::firstOrCreate([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
            ]);

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_liked_successfully') ?? 'Post liked successfully','status'=>true],200);
            } catch(\Exception $e)  {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }

    public function comment(Request $request)
        {
            try {
            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            $comment = PostComment::create([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
                'comment' => $request->comment,
            ]);

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_comment_added_successfully') ?? 'Comment added successfully','status'=>true, 'comment' => $comment]);
            } catch(\Exception $e)  {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }

    public function share(Request $request)
        {
            try {
            PostShare::create([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
            ]);

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_shared_successfully') ?? 'Post shared successfully','status'=>true],200);
            } catch(\Exception $e)  {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }
    public function unlike(Request $request)
        {
            try {
            $like = PostLike::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();
            if (!$like) {
                return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_like_not_found') ?? 'Like not found','status'=>false], 400);
            }

            $like->delete();
            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_unliked_successfully') ?? 'Post unliked successfully','status'=>true],200);
            } catch(\Exception $e)  {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }
    public function deleteComment(Request $request)
        {
            try {
            $comment = PostComment::find($request->comment_id);
            if (!$comment) {
                return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ??'Comment not found','status'=>false], 400);
            }
           
            $comment->delete();
            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_comment_deleted_successfully') ??'Comment deleted successfully','status'=>true],200);
            } catch(\Exception $e)  {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }
    

        public function translate(Request $request){
            // ✅ Validate required inputs
            $request->validate([
                'caption' => 'required|string',
                'target_lang' => 'required|string', // Example: 'hi', 'fr', 'es'
            ]);

            try {
                // ✅ Call translation helper/service
                $translated = translateMessageWithOpenAI($request->caption, $request->target_lang, '');
                
                // Not condition add kar rakha hoonn mene jaab translate ki api aa jayegi taab me hata dunga 
                if ($translated) {
                    return response()->json([
                        'status'     => true,
                        'original'   => $request->caption,
                        'translated' => $translated, //!$translated
                        'lang'       => $request->target_lang,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'error'  => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_translation_failed') ?? 'Translation failed.',
                    ], 500);
                }
            } catch (\Exception $e) {
                 return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }
    
        public function showCommentUser(Request $request){
            try {
                $comments = PostComment::where('post_id', $request->post_id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($comments->isEmpty()) {
                    return response()->json([
                        'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_no_comments_found') ?? 'No comments found for this post.',
                        'status' => false,
                        'data' => []
                    ], 404);
                }

                return response()->json([
                    'message' => 'Successfully fetched comments.',
                    'status' => true,
                    'data' => $comments
                ], 200);

            } catch(\Exception $e) {
                 return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }


    public function showLikeUser(Request $request){
        try {
            $likes = PostLike::where('post_id', $request->post_id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($likes->isEmpty()) {
                return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_no_likes_found') ?? 'No likes found for this post.',
                    'status' => false,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_likes_fetched_successfully') ?? 'Successfully fetched likes.',
                'status' => true,
                'data' => $likes
            ], 200);

        } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }


    public function postDetail(Request $request){
        try {
            $post = Posts::with(['media', 'user', 'likes', 'comments'])
                ->where('id', $request->post_id)
                ->first();

            if (!$post) {
                return response()->json([
                    'message' => 'Post not found.',
                    'status' => false,
                    'data' => null
                ], 404);
            }

            // Format media URLs
            $media_urls = [];
            if ($post->post_type === 'photo' || $post->post_type === 'video') {
                if ($post->media_path) {
                    $media_urls[] = Storage::disk('s3')->url($post->media_path);
                }
            } elseif ($post->post_type === 'carousel') {
                $media_urls = $post->media->map(function ($media) {
                    return Storage::disk('s3')->url($media->media_path);
                });
            }

            // Prepare formatted post data
            $formattedPost = [
                'id'            => $post->id,
                'user'          => $post->user,
                'post_type'     => $post->post_type,
                'content'       => $post->content,
                'caption'       => $post->caption,
                'media'         => $media_urls,
                'like_count'    => $post->likes->count(),
                'comment_count' => $post->comments->count(),
                'created_at'    => $post->created_at->toDateTimeString(),
            ];

            $likes = PostLike::with('user')->where('post_id', $request->post_id)->orderBy('created_at', 'desc')->get();
            $comments = PostComment::with('user')->where('post_id', $request->post_id)->orderBy('created_at', 'desc')->get();

            // Response
            return response()->json([
                'message'       => 'Successfully fetched post.',
                'status'        => true,
                'base_url'      => Storage::disk('s3')->url(''),
                'data'          => $formattedPost,
                'likes'         => $likes,
                'comments'      => $comments,
            ], 200);

        } catch (\Exception $e) {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function PostReportSubmit(Request $request){
        try {
            $request->validate([
                'post_id' => 'required|exists:posts,id',
                'reason' => 'required|string|max:255',
            ]);

            // Optional: prevent duplicate reports from same user
            $existingReport = PostReports::where('user_id', $request->user_id)
                                    ->where('post_id', $request->post_id)
                                    ->first();

            if ($existingReport) {
                return response()->json([
                    'status' => false,
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_already_reported') ?? 'You have already reported this post.'
                ], 409);
            }

            $report = PostReports::create([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
                'reason' => $request->reason,
            ]);

            return response()->json([
                'status' => true,
                'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_reported_successfully') ?? 'Post reported successfully.',
                'data' => $report
            ], 200);
        } catch (\Exception $e) {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function BlockPostContent(Request $request){
        try {
            $request->validate([
                'post_id' => 'required|exists:posts,id',
            ]);

            // check if already blocked
            $already = PostBlock::where('user_id', $request->user_id)
                                ->where('post_id', $request->post_id)
                                ->first();

            if ($already) {
                return response()->json([
                    'status' => false,
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_already_blocked') ?? 'Post already blocked.'
                ], 409);
            }

            $block = PostBlock::create([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
                'text' => $request->text??'',

            ]);

            return response()->json([
                'status' => true,
                'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_post_blocked_successfully') ?? 'Post blocked successfully.',
                'data' => $block
            ]);
        } catch (\Exception $e) {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }


}