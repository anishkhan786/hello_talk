<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\LmsQuestions;
use App\Models\LmsQuestionAnswers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
class LmsQuestionApiController extends Controller
{
 public function lms_question_list(Request $request){
    try {
       
        $category_id = $request->has('category_id')?$request->category_id:'';
        $response = LmsQuestions::where('is_active', '1')->where('category_id', $category_id)->get();

        if(!empty($response)){
            $response = ['message'=> 'success.','status'=>true,'data' => $response];
            return response($response, 200);
        } else {
            $response = ["message" => "LMS Questions not exit",'status'=>FALSE];
            return response($response, 422);
        }
    } catch(\Exception $e)  {
        $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
        return response($response, 400);
    }
 }

 public function lms_question_submit(Request $request)
    {
        try {
              $data = LmsQuestions::find($request['question_id']);
               $correct_answer = $data->correct_answer;

            if($correct_answer == $request['selected_answer']){
                $is_correct = '1';
              } else {
                $is_correct = '2';
              }
            LmsQuestionAnswers::create([
                'question_id' => $request['question_id'],
                'user_id' => $request['user_id'],
                'selected_answer'=>$request['selected_answer'],
                'is_correct'=>$is_correct,
            ]);
            $response = ['message'=> 'success', 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
            $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
            return response($response, 400);
        }
    }


}