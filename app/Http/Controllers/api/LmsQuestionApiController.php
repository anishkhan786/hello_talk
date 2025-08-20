<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\LmsQuestions;
use App\Models\LmsQuestionAnswers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\McqTopic;
use App\Models\McqQuestion;
use App\Models\McqOption;
use App\Models\McqUserAnswer;
use App\Models\McqCompleteTopicPage;
use App\Models\CourseDemoDetails;

class LmsQuestionApiController extends Controller
{
 
 public function mcq_questions(Request $request){
    try {
        $perPage =  $request->per_page??10;

        $topic_id = $request->has('topic_id')?$request->topic_id:'';
        $response = McqQuestion::with('mcqOptions')->where('topic_id', $topic_id)->paginate($perPage);

        if(!empty($response)){

             // Format mcq
            $formattedQuestions = $response->getCollection()->map(function ($res) {
            $options = [];
            $correctAnswer = 0;

                foreach ($res->mcqOptions as $key => $value) {
                    $options[++$key] = $value->option_text;

                    if($value->is_correct == '1'){
                        $correctAnswer = $key;
                    }
                }

                return [
                    'id' => $res->id,
                    'question' => $res->title,
                    'options' => $options,
                    'correctAnswer' => $correctAnswer
                ];
            });
            // $response = json_decode($response, true);

             return response()->json([
                        'message' => 'Questions fetched successfully',
                        'status' => true,
                        'data' => $formattedQuestions,
                        'next_page_url'=> $response->nextPageUrl(),
                        'current_page' => $response->currentPage(),
                        'last_page' => $response->lastPage(),
                        'per_page' => $response->perPage(),
                        'total' => $response->total(),
                        'has_more' => $response->currentPage() < $response->lastPage()
                    ], 200);
        } else {
            $response = ["message" => "topic not exit",'status'=>FALSE];
            return response($response, 422);
        }
    } catch(\Exception $e)  {
        $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
        return response($response, 400);
    }
 }

  public function topics(Request $request){
    try {
       
        $language_id = $request->has('language_id')?$request->language_id:'';
        $learning_level = $request->has('learning_level_id')?$request->learning_level_id:'';
        $user_id = $request->has('user_id')?$request->user_id:'';

        $response = McqTopic::where('language_id', $language_id)
            ->where('learning_level', $learning_level)
            ->get();

        if ($response->isNotEmpty()) {
            $formattedTopics = $response->map(function ($res) use ($user_id) {
                $page_data = McqCompleteTopicPage::where('topic_id', $res->id)
                    ->where('user_id', $user_id)
                    ->first();

                if (!empty($page_data)) {
                    $page = $page_data->page_number;
                } else {
                    $page = 0;
                }

                return [
                    'id' => $res->id,
                    'name' => $res->name,
                    'description' => $res->description,
                    'completed_page' => $page,
                ];
            });

            $response = [
                'message' => 'success.',
                'status'  => true,
                'data'    => $formattedTopics,
            ];
            return response($response, 200);
        } else {
            return response([
                'message' => 'No topics found.',
                'status'  => false,
                'data'    => [],
            ], 200);
        }

    } catch(\Exception $e)  {
        $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
        return response($response, 400);
    }
 }
 

 
    public function mcq_answers_submit(Request $request)
    {
        try {
              $topic_id = $request->has('topic_id')?$request->topic_id:'';
              $user_id = $request->has('user_id')?$request->user_id:'';
              $page = $request->has('page_number')?$request->page_number:'';
            
            $page_data = McqCompleteTopicPage::where('topic_id',$topic_id)->where('user_id',$user_id)->first();
                if(!empty($page_data)){
                    $page_data->delete();
                }

             McqCompleteTopicPage::create([
                'topic_id' => $topic_id,
                'user_id' =>  $user_id,
                'page_number'=> $page,
            ]);

            $response = ['message'=> 'success', 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
            $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
            return response($response, 400);
        }
    }

    public function course_demo_details(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'email' => 'required|email',
            'mobile_number' => 'required|string|max:15',
            'dob' => 'required|string',
            'learn_language_id' => 'required|integer',
            'learning_level_id' => 'required|integer',
            'why_are_you_learing_this_language_id' => 'required|integer',
            'country_id' => 'required|integer',
        ]);

        // Step 2: If basic validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

        $detail = CourseDemoDetails::create([
                'user_id' => $request->has('user_id')?$request->user_id:'',
                'email' =>  $request->has('email')?$request->email:'',
                'mobile_number'=> $request->has('mobile_number')?$request->mobile_number:'',
                'dob'=> $request->has('dob')?$request->dob:'',
                'learn_language_id'=> $request->has('learn_language_id')?$request->learn_language_id:'',
                'learning_level_id'=> $request->has('learning_level_id')?$request->learning_level_id:'',
                'why_are_you_learing_this_language_id'=>$request->has('why_are_you_learing_this_language_id')?$request->why_are_you_learing_this_language_id:'',
                'country_id'=>$request->has('country_id')?$request->country_id:'',
            ]);

        return response()->json(['status' => true, 'message' => 'Created successfully', 'data' => $detail], 200);
    }

//  public function mcq_answers(Request $request)
//     {
//         try {
//               $question_id = $request->has('question_id')?$request->question_id:'';
//               $option_id = $request->has('option_id')?$request->option_id:'';
//               $user_id = $request->has('user_id')?$request->user_id:'';
//               $answer_text = $request->has('answer_text')?$request->answer_text:'';

              
//                 if (empty($option_id) || empty($question_id) || empty($user_id)) {
//                     $response = ['response' => array(),'message'=>'Option id, User Id, Question id, is Empty','status'=>false];
//                     return response($response, 400);
//                 }

//                 $answer_data = McqUserAnswer::where('user_id',$user_id)->where('question_id',$question_id)->first();
//                 if(!empty($answer_data)){
//                     $answer_data->delete();
//                 }

//                 $res = McqOption::with('question')->where('question_id',$question_id)->where('id',$option_id)->first();
            
//                 if(!empty($res)){
//                     if($res->question->type == 'mcq'){
//                         if($res->is_correct==1){
//                             $is_correct = '1';
//                         } else {
//                             $is_correct = '2';
//                         }
//                     } else {
//                         $is_correct = '2';
//                     }
                    
//                 } else {
//                     $is_correct = '2';
//                 }

//             McqUserAnswer::create([
//                 'question_id' => $question_id,
//                 'user_id' =>  $user_id,
//                 'answer_text'=> $answer_text,
//                 'option_id'=> $option_id,
//                 'is_correct'=>$is_correct,
//             ]);

//             $response = ['message'=> 'success', 'status'=>200];
//             return response($response, 200);
//         } catch(\Exception $e)  {
//             $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
//             return response($response, 400);
//         }
//     }


}