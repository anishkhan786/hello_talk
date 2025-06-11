<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Inquiries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquirieApiController extends Controller
{
 public function store(Request $request)
    {
        Inquiries::create([
            'user_id' => $request['user_id'],
            'phone' => $request['phone'],
            'subject' => $request['subject'],
            'message'=> $request['message'],
        ]);

        $response = ['message'=> 'success', 'status'=>200];
        return response($response, 200);
    }
}