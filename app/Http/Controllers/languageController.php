<?php

namespace App\Http\Controllers;

use App\Models\languag;
use Illuminate\Http\Request;

class languageController extends Controller
{
    public function index(){
        $data = languag::paginate(10);
        return view('admin/language/index',compact('data'));
    }

    public function add(){
        return view('admin.language.Add');
    }

    public function store(Request $request){
        $data = new languag();
        $data->name = $request->name;
        $data->code = $request->code;
        $data->save();
        return redirect()->back()->with('success', 'Language created successfully.');
    }

    public function edit($id){
        $data = languag::find($id);
        return view('admin.language.edit',compact('data'));
    }
    public function update(Request $request){
        $data = languag::find($request->id);
        $data->name = $request->name;
        $data->code = $request->code;
        $data->save();
        return redirect()->back()->with('success', 'Language updated successfully.');
    }

    public function delete($id){
        $data = languag::find($id);
        $user = $data->delete();
        return redirect()->back()->with('warning','Language deleted.');
    }
}
