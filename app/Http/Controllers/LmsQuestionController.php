<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\LmsQuestions;
use App\Models\Category;
use App\Models\Course;

class LmsQuestionController extends Controller
{
    public function index()
    {
        $data = LmsQuestions::with('course','category')->paginate(10);
        return view('admin/LmsQuestion/index',compact('data'));
    }

    public function create()
    {
        $course = Course::get();
        return view('admin.LmsQuestion.add', compact('course'));
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'question_text' => 'required',
            'correct_answer'=>'required'
        ]);

        LmsQuestions::create([
            'question_text' => $attributes['question_text'],
            'course_id' => $request->get('course_id'),
            'category_id' => $request->get('category_id'),
            'marks' => $request->get('marks'),
            'explanation' => $request->get('explanation'),
            'option_a' => $request->get('option_a'),
            'option_b' => $request->get('option_b'),
            'option_c' => $request->get('option_c'),
            'option_d' => $request->get('option_d'),
            'correct_answer' => $attributes['correct_answer'],
            'is_active' => $request->get('is_active'),
        ]);

        return redirect()->back()->with('success', 'LMS Questions succesfully added');
    }

    public function edit($id)
    {
       
        $data = LmsQuestions::find($id);
        $course = Course::get();
        $Category = Category::where('course_id', $data->course_id)->get();
        return view('admin.LmsQuestion.edit', compact('data','course','Category'));
    }

    public function update(Request $request, $id)
    {
        $category = LmsQuestions::find($id);
        $request_data = array(
                'question_text' => $request['question_text'],
                'course_id' => $request->get('course_id'),
                'category_id' => $request->get('category_id'),
                'marks' => $request->get('marks'),
                'explanation' => $request->get('explanation'),
                'option_a' => $request->get('option_a'),
                'option_b' => $request->get('option_b'),
                'option_c' => $request->get('option_c'),
                'option_d' => $request->get('option_d'),
                'correct_answer' => $request['correct_answer'],
                'is_active' => $request->get('is_active'),
            );
        $category->update($request_data);

        return redirect()->back()->with('success', 'LMS Questions succesfully updated');
    }

    public function destroy($id)
    {
        $category = LmsQuestions::find($id);
        $category->delete();
        return redirect()->back()->with('warning','The LMS Questions was succesfully deleted.');
    }

    public function getCategories($course_id)
    {
        $categories = Category::where('course_id', $course_id)->get(['id', 'name']);
        return response()->json($categories);
    }
}
