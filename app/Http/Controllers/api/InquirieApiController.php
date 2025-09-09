<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Inquiries;
use Illuminate\Http\Request;
use App\Models\HelperLanguage;
use Illuminate\Support\Facades\Auth;

class InquirieApiController extends Controller
{
 public function store(Request $request)
    {
        try{
            Inquiries::create([
                'user_id' => $request['user_id'],
                'phone' => $request['phone'],
                'subject' => $request['subject'],
                'message'=> $request['message'],
            ]);

            $response = ['message'=> 'success', 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }
}