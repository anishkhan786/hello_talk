<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\McqTopic;
use App\Models\languag;
use App\Models\learningLevel;

class QuestionTopicController extends Controller
{
    public function index(Request $request)
        {
            $query = McqTopic::with(['language','learninglevel']);

            // Name filter
            if ($request->name) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            // Description filter
            if ($request->description) {
                $query->where('description', 'like', '%' . $request->description . '%');
            }

            // Language filter
            if ($request->language_id) {
                $query->where('language_id', $request->language_id);
            }

            // Learning Level filter
            if ($request->learning_level_id) {
                $query->where('learning_level', $request->learning_level_id);
            }

            // Pagination ke sath filter values preserve karne ke liye appends()
            $data = $query->paginate(10)->appends($request->all());

            // dropdowns ke liye data bhejna hoga
            $languages = languag::all();
            $levels = learningLevel::all();

            return view('admin.question_topic.index', compact('data','languages','levels'));
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

    public function delete(McqTopic $McqTopic,$id){
        $data = McqTopic::find($id);
        $user = $data->delete();
        return redirect()->back()->with('warning','Question Topic deleted.');
    }
}
