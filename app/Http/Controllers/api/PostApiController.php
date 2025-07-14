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
    if (!empty($request->type) && $request->type == 'Favorites') {
        $post_ids = PostLike::where('user_id', $user->id)->pluck('post_id');
        $posts = Posts::whereIn('id', $post_ids)
                    ->with('media', 'user', 'likes', 'comments')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    } else {
        // Get all users this user follows
        $followingIds = Follow::where('follower_id', $user->id)->pluck('following_id');

        // Paginated posts from followed users
        $posts = Posts::whereIn('user_id', $followingIds)
                    ->with('media', 'user', 'likes', 'comments')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    // Format posts
    $formattedPosts = $posts->getCollection()->map(function ($post) use ($user) {
        $media_urls = [];

        if ($post->post_type === 'photo' || $post->post_type === 'video') {
            if ($post->media_path) {
                $media_urls[] = asset('storage/app/public/' . $post->media_path);
            }
        } elseif ($post->post_type === 'carousel') {
            $media_urls = $post->media->map(function ($media) {
                return asset('storage/app/public/' . $media->media_path);
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
            'content' => $post->content,
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
                        $media_urls[] = asset('storage/app/public/' . $post->media_path);
                    }
                } elseif ($post->post_type === 'carousel') {
                    $media_urls = $post->media->map(function ($media) {
                        return asset('storage/app/public/' . $media->media_path);
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
                'post_type' => 'required|in:text,photo,video,carousel',
                'content' => 'nullable|string',
            ]);

            $post = Posts::create([
                'user_id' => $request->user_id,
                'post_type' => $request->post_type,
                'content' => $request->content,
                'caption' => $request->caption,
                'location' => $request->location,

            ]);

            if ($request->post_type === 'photo' || $request->post_type === 'video') {
                if ($request->hasFile('media')) {
                    $mediaPath = $request->file('media')->store('posts', 'public');
                    $post->update(['media_path' => $mediaPath]);
                }
            }

            if ($request->post_type === 'carousel') {
                if ($request->hasFile('media')) {
                    foreach ($request->file('media') as $file) {
                        $mediaPath = $file->store('posts/carousel', 'public');
                        PostMedia::create([
                            'post_id' => $post->id,
                            'media_path' => $mediaPath
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Post created','status'=>true],200);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
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

            return response()->json(['message' => 'Post updated','status'=>true, 'post' => $post],200);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

    public function destroy(Request $request)
        {
            try {
            $post = Posts::with('media')->findOrFail($request->post_id);

            if ($post->user_id != auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Delete media files
            if ($post->post_type == 'carousel') {
                foreach ($post->media as $media) {
                    Storage::disk('public')->delete($media->media_path);
                    $media->delete();
                }
            } else {
                Storage::disk('public')->delete($post->media_path);
            }

            $post->delete();

            return response()->json(['message' => 'Post deleted','status'=>true],200);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

    public function like(Request $request)
        {
            try {
            $like = PostLike::firstOrCreate([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
            ]);

            return response()->json(['message' => 'Post liked','status'=>true],200);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
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

            return response()->json(['message' => 'Comment added','status'=>true, 'comment' => $comment]);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

    public function share(Request $request)
        {
            try {
            PostShare::create([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
            ]);

            return response()->json(['message' => 'Post shared','status'=>true],200);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }
    public function unlike(Request $request)
        {
            try {
            $like = PostLike::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();
            if (!$like) {
                return response()->json(['message' => 'Like not found','status'=>false], 400);
            }

            $like->delete();
            return response()->json(['message' => 'Post unliked successfully','status'=>true],200);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }
    public function deleteComment(Request $request)
        {
            try {
            $comment = PostComment::find($request->comment_id);
            if (!$comment) {
                return response()->json(['message' => 'Comment not found','status'=>false], 400);
            }
           
            $comment->delete();
            return response()->json(['message' => 'Comment deleted successfully','status'=>true],200);
            } catch(\Exception $e)  {
                $response = ['message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }
    

    public function translate(Request $request)
            {
                $request->validate([
                    'caption' => 'required|string',
                    'target_lang' => 'required|string' // e.g., "hi", "fr", "es"
                ]);

                $translated = translateMessageWithOpenAI($request->caption, $request->target_lang, '');
                if ( $translated) {
                    return response()->json([
                        'status'=>true,
                        'original' => $request->caption,
                        'translated' => $translated,
                        'lang' => $request->target_lang,
                    ],200);
                } else {
                     return response()->json(['status'=>false,'error' => 'Translation failed'], 500);
                }

            }


}