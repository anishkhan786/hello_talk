<?php

namespace App\Http\Controllers;

use App\Models\contry;
use App\Models\languag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $data = User::where('type', 'user')->paginate(10);
        return view('admin.user.index', compact('data'));
    }

    public function edit($id)
    {
        $data = User::find($id);
        $languages = languag::all();
        $countries = contry::all();
        return view('admin.user.edit', compact('data', 'languages', 'countries'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            // 'password' => 'required|string',
            'bio' => 'nullable|string',
            'native_language' => 'nullable|string',
            'learning_language' => 'nullable|string',
            'country' => 'nullable|string',
            'gender' => 'nullable|string',
            'dob' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);
        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        // $user->password = $request->password;
        $user->bio = $request->bio;
        $user->native_language = $request->native_language;
        $user->learning_language = $request->learning_language;
        $user->country = $request->country;
        $user->gender = $request->gender;
        $user->dob = $request->dob;


        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $sanitizedName = preg_replace('/\s+/', '_', $originalName); // replace spaces with underscores
            $extension = $file->getClientOriginalExtension();

            $filename = time() . '_' . $sanitizedName . '.' . $extension;

            // Ensure directory exists
            if (!file_exists(public_path('avatars'))) {
                mkdir(public_path('avatars'), 0777, true);
            }

            $file->move(public_path('avatars'), $filename);
            $user->avatar = 'avatars/' . $filename;
        }


        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function delete($id)
    {
        $data = User::find($id);
        $user = $data->delete();
        return redirect()->back()->with('warning', 'User deleted.');
    }
}
