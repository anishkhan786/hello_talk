<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // dd('test');
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'native_language' => 'required|string',
                'learning_language' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'status' => false
            ]);
        }
        // dd($request);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'native_language'  => $request->native_language,
            'learning_language'  => $request->learning_language,
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
            'status' => true
        ]);
    }

    public function login(Request $request)
    {
        try{
            $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        }catch(ValidationException $e){
             return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password',
                'status' => false,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'status' => true,
            'token'   => $token,
        ]);
    }

    public function logout(Request $request)
{
    // Revoke the token that was used to authenticate the current request
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully',
    ]);
}
}
