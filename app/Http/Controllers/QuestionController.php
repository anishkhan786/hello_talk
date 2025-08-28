<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\McqTopic;
use App\Models\McqQuestion;
use App\Models\McqOption;
use App\Models\McqUserAnswer;

class QuestionController extends Controller
{
        public function index()
    {
        $questions = McqQuestion::with('options')->latest()->paginate(10);
        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        $topic = McqTopic::get();
        return view('admin.questions.add' , compact('topic'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'topic_id'=>'required',
            'type' => 'required|in:mcq,true_false,fill_blank,short_answer,matching',
            'marks' => 'nullable|integer|min:0'
        ]);

        DB::transaction(function() use ($request) {
            $question = McqQuestion::create($request->only('title','description','type','marks','topic_id'));

            // save options when required
            if (in_array($request->type, ['mcq','true_false','matching'])) {
                $options = $request->input('options', []);
                foreach ($options as $idx => $opt) {
                    if (trim($opt['option_text'] ?? '') === '') continue;
                    $question->options()->create([
                        'option_text' => $opt['option_text'] ?? null,
                        'is_correct' => !empty($opt['is_correct']) ? 1 : 0,
                        'match_key' => $opt['match_key'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('questions')->with('success','Question created');
    }

    public function edit(McqQuestion $question, $id)
    {
        $question = McqQuestion::find($id);
        $question->load('options');
        $topic = McqTopic::get();
        return view('admin.questions.edit', compact('question','topic'));
    }

    public function update(Request $request, $id)
    {
         $question = McqQuestion::find($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'topic_id'=>'required',
            'type' => 'required|in:mcq,true_false,fill_blank,short_answer,matching',
            'marks' => 'nullable|integer|min:0'
        ]);

        DB::transaction(function() use ($request, $question) {
           $question->update($request->only('title','description','type','marks','topic_id'));
            
            // delete old options and re-insert (simple approach)
            if (in_array($request->type, ['mcq','true_false','matching'])) {
                $question->options()->delete();
                $options = $request->input('options', []);
                foreach ($options as $idx => $opt) {
                    if (trim($opt['option_text'] ?? '') === '') continue;
                    $question->options()->create([
                        'question_id'=>$question->id,
                        'option_text' => $opt['option_text'] ?? null,
                        'is_correct' => !empty($opt['is_correct']) ? 1 : 0,
                        'match_key' => $opt['match_key'] ?? null,
                    ]);
                }
            } else {
                // if new type doesn't need options, ensure old options deleted
                $question->options()->delete();
            }
        });

        return redirect()->route('questions')->with('success','Question updated');
    }

    public function destroy(McqQuestion $question, $id)
    {
        $item = McqOption::where('question_id',$id)->delete();
        $item = McqQuestion::where('id',$id)->delete();
        return redirect()->route('questions')->with('success','Question deleted');
    }

    public function show(McqQuestion $question,$id)
    {
        $question = McqQuestion::find($id);
        $question->load('options');
        return view('admin.questions.show', compact('question'));
    }
}