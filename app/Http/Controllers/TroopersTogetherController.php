<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TroopersTogether;
use App\Models\Category;

class TroopersTogetherController extends Controller
{
    public function index()
    {
        $data = TroopersTogether::paginate(10);
        return view('admin/TrooperTogether/index',compact('data'));
    }

    public function create()
    {
        return view('admin.TrooperTogether.add');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'group_title' => 'required',
        ]);

        TroopersTogether::create([
            'group_title' => $attributes['group_title'],
            'group_description' => $request->get('group_description'),
        ]);

        return redirect()->back()->with('success', 'Troopers Together succesfully added');
    }

    public function edit($id)
    {
       
        $data = TroopersTogether::find($id);
        return view('admin.TrooperTogether.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $category = TroopersTogether::find($id);
        $request_data = array(
                'group_title' => $request->get('group_title'),
                'group_description' => $request->get('group_description')
            );
        $category->update($request_data);

        return redirect()->back()->with('success', 'Troopers Together succesfully updated');
    }

    public function destroy($id)
    {
        $category = TroopersTogether::find($id);
        $category->delete();
        return redirect()->back()->with('warning','The Troopers Together was succesfully deleted.');
    }
}
