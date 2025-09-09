<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\contry;
use App\Models\languag;
use Illuminate\Http\Request;
use App\Models\HelperLanguage;

class CountryController extends Controller
{
    public function contry(){
        try {
            $data = contry::all();

            return response()->json([
                'status' => true,
                'code'   => 200,
                'contry' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function language(){
         try {
            $data = languag::all();

            return response()->json([
                'status'   => true,
                'code'     => 200,
                'base_url' => asset('storage'),
                'language' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }
}
