<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\McqTopic;
use App\Models\languag;
use App\Models\learningLevel;

class QuestionTopicController extends Controller
{
    public function index(){
        $data = McqTopic::with('language','learninglevel')->paginate(10);
        return view('admin.question_topic.index',compact('data'));
    }

    public function create(){
        $language = languag::get();
        $learning_level = learningLevel::get();
        return view('admin.question_topic.add', compact('language','learning_level'));
    }

    public function store(Request $request){
        $data = new McqTopic();
        $data->name = $request->name;
        $data->description = $request->description;
        $data->learning_level = $request->learning_level;
        $data->language_id = $request->language_id;
        $data->save();
        return redirect()->back()->with('success', 'Question Topic created successfully.');
    }

    public function edit($id){
        $data = McqTopic::find($id);
         $language = languag::get();
        $learning_level = learningLevel::get();
        return view('admin.question_topic.edit',compact('data','language','learning_level'));
    }
    public function update(Request $request){
        $data = McqTopic::find($request->id);
        $data->name = $request->name;
        $data->description = $request->description;
        $data->learning_level = $request->learning_level;
        $data->language_id = $request->language_id;
        $data->save();
        return redirect()->back()->with('success', 'Question Topic updated successfully.');
    }

    public function delete($id){
        $data = McqTopic::find($id);
        $user = $data->delete();
        return redirect()->back()->with('warning','Question Topic deleted.');
    }
}
