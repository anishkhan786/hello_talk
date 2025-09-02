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
        return view('admin.language.add');
    }

    public function store(Request $request){

        $request->validate([
                'language_file' => [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        if ($value->getClientOriginalExtension() !== 'arb') {
                            $fail('Only .arb files are allowed.');
                        }
                    },
                ],
            ]);

        $file = $request->file('language_file');
        // get original file name
        $originalName = 'app_'.$request->code.'.arb';
        // force save as .arb
        $attachmentPath = $file->storeAs('arb', $originalName, 'public');

        $data = new languag();
        $data->name = $request->name;
        $data->code = $request->code;
        $data->arb_url = $attachmentPath;
        $data->flag_emoji = $request->flag_emoji;
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
        $data->flag_emoji = $request->flag_emoji;

        if ($request->hasFile('language_file')) {
            // Agar file already exist hai toh delete kar do
            if (!empty($data->arb_url) AND \Storage::disk('public')->exists($data->arb_url)) {
                \Storage::disk('public')->delete($data->arb_url);
            }

            $file = $request->file('language_file');
            // get original file name
            $originalName = 'app_'.$request->code.'.arb';
            // force save as .arb
            $attachmentPath = $file->storeAs('arb', $originalName, 'public');        
            $data->arb_url = $attachmentPath;   
        }

        $data->save();
        return redirect()->back()->with('success', 'Language updated successfully.');
    }

    public function delete($id){
        $data = languag::find($id);
        $user = $data->delete();
        return redirect()->back()->with('warning','Language deleted.');
    }
}
