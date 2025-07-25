<?php

namespace App\Http\Controllers;

use App\Models\languag;
use App\Models\Posts;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\PostShare;

use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function index(){
        $data = Posts::with('media', 'user')->paginate(10);
       
        return view('admin/post/index',compact('data'));
    }

    public function delete($id){
        $post = Posts::with('media')->findOrFail($id);

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

        return redirect()->back()->with('warning','Post deleted.');
    }

}
