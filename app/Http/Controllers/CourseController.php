<?php

namespace App\Http\Controllers;

use App\Models\learningLevel;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(){
        $data = learningLevel::paginate(10);
        return view('admin/course/index',compact('data'));
    }

    public function add(){
        return view('admin.course.add');
    }

    public function store(Request $request){
        $data = new learningLevel();
        $data->name = $request->name;
        $data->save();
        return redirect()->back()->with('success', 'Course created successfully.');
    }

    public function edit($id){
        $data = learningLevel::find($id);
        return view('admin.course.edit',compact('data'));
    }
    public function update(Request $request){
        $data = learningLevel::find($request->id);
        $data->name = $request->name;
        $data->save();
        return redirect()->back()->with('success', 'Course updated successfully.');
    }

    public function delete($id){
        $data = learningLevel::find($id);
        $user = $data->delete();
        return redirect()->back()->with('warning','Course deleted.');
    }
}
