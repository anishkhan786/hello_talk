<?php

namespace App\Http\Controllers;

use App\Models\contry;
use Illuminate\Http\Request;

class contryController extends Controller
{
    public function index(){
        $data = contry::paginate(10);
        return view('admin/contry/index',compact('data'));
    }

    public function add(){
        return view('admin.contry.Add');
    }

    public function store(Request $request){
        $data = new contry();
        $data->name = $request->name;
        $data->code = $request->code;
        $data->save();
        return redirect()->back()->with('success', 'Country created successfully.');
    }

    public function edit($id){
        $data = contry::find($id);
        return view('admin.contry.edit',compact('data'));
    }
    public function update(Request $request){
        $data = contry::find($request->id);
        $data->name = $request->name;
        $data->code = $request->code;
        $data->save();
        return redirect()->back()->with('success', 'Country updated successfully.');
    }

    public function delete($id){
        $data = contry::find($id);
        $user = $data->delete();
        return redirect()->back()->with('warning','Country deleted.');
    }
}
