<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\learningLevel;

class CategoryController extends Controller
{
    public function index()
    {
        $data = Category::with('course')->paginate(10);
        return view('admin/category/index',compact('data'));
    }

    public function create()
    {
        $course = learningLevel::get();
        return view('admin.category.add', compact('course'));
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'required',
            'course_id' => 'required',

        ]);

        Category::create([
            'name' => $attributes['name'],
            'course_id' => $attributes['course_id'],

        ]);

        return redirect()->route('category')->with('succes', 'Category succesfully added');
    }

    public function edit($id)
    {
        $course = learningLevel::get();
        $data = Category::find($id);
        return view('admin.category.edit', compact('data','course'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $request_data = array(
                'name' => $request->get('name'),
                'course_id' => $request->get('course_id')
            );
        $category->update($request_data);

        return redirect()->route('category')->with('succes', 'Category succesfully updated');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        return redirect()->back()->with('warning','The category was succesfully deleted.');
    }
}
