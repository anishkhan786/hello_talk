<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TroopersTogether;
use App\Models\Category;
use App\Models\languag;
use App\Models\UserGroup;
use App\Models\GroupsMessages;
use App\Models\GroupSettings;
use App\Models\User;

class TroopersTogetherController extends Controller
{
    public function index()
    {
        $data = TroopersTogether::paginate(10);
        return view('admin/TrooperTogether/index',compact('data'));
    }

    public function create()
    {
        $language = languag::get();
        return view('admin.TrooperTogether.add',compact('language'));
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'group_title' => 'required',
        ]);

        TroopersTogether::create([
            'group_title' => $attributes['group_title'],
            'language_id'=>$request->get('language_id'),
            'group_description' => $request->get('group_description'),
        ]);

        return redirect()->back()->with('success', 'Troopers Together succesfully added');
    }

    public function edit($id)
    {
       
        $data = TroopersTogether::find($id);
        $language = languag::get();
        return view('admin.TrooperTogether.edit', compact('data','language'));
    }

    public function group_member($id)
    {
        $group_data = TroopersTogether::find($id);
        $data = UserGroup::with('user')->where('group_id', $id)->whereHas('user')->paginate(20);
        // $data = User::whereIn('id', $group_member)->paginate(20);
        return view('admin/TrooperTogether/show_group_member',compact('data','group_data'));
    }

    public function group_member_destroy($id)
    {
        $UserGroup = UserGroup::where('id', $id)->first();
        $UserGroup->update(array('block_admin' => 2));
        return redirect()->back()->with('warning','Group member blocked successfully.');
    }

    public function group_member_unblock($id)
    {
        $UserGroup = UserGroup::where('id', $id)->first();
        $UserGroup->update(array('block_admin' => 1));
        return redirect()->back()->with('warning','Group member unblocked successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = TroopersTogether::find($id);
        $request_data = array(
                'group_title' => $request->get('group_title'),
                'language_id'=>$request->get('language_id'),
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
