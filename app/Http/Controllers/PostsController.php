<?php

namespace App\Http\Controllers;

use App\Models\languag;
use App\Models\Posts;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\PostShare;
use App\Models\CourseDemoDetails;
use App\Models\PostReports;

use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function index(){
        $data = Posts::with('media', 'user')->paginate(10);
       
        return view('admin/post/index',compact('data'));
    }

    public function post_report(){
        $data = PostReports::with('user', 'post')->paginate(10);
        return view('admin/post/post_report',compact('data'));
    }

     public function courseDemoDetails(){
        $data = CourseDemoDetails::with('user','learningLevel', 'language','country')->paginate(10);
        return view('admin/post/course_demo_details',compact('data'));
    }

    public function delete($id){
        $post = Posts::with('media','post_reports')->findOrFail($id);

            // Delete media files
            if ($post->post_type == 'carousel') {
                foreach ($post->media as $media) {
                    if(!empty($post->media_path)){
                        Storage::disk('s3')->delete($post->media_path);
                    }
                    $media->delete();
                }
            } else {
                if(!empty($post->media_path)){
                    Storage::disk('s3')->delete($post->media_path);
                }
                
            }

            $post->delete();

        return redirect()->back()->with('warning','Post deleted.');
    }

}
