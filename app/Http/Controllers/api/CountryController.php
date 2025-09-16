<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\contry;
use App\Models\languag;
use Illuminate\Http\Request;
use App\Models\HelperLanguage;
use Illuminate\Support\Facades\Storage;

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
public function FileUpload(Request $request)
{
    try {

        // Upload file to S3 (private by default)
        $path = $request->file('file')->store('uploads', 's3');

        // Optionally, get public URL
        // If bucket allows public access:
        // $url = Storage::disk('s3')->url($path);

        // If bucket is private, generate temporary URL (recommended)
        // $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));

        Storage::disk('s3')->delete('uploads/28EujYzHfhouPE7wYonzq3ErpLxeQ2o6mzwuETGy.jpg');

        return response()->json([
            'message' => 'File uploaded successfully!',
            's3-url' => Storage::disk('s3')->url( $path),
            'path' => $path,
            'url' => ''

        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => HelperLanguage::retrieve_message_from_arb_file(
                $request->language_code,
                'web_internal_error'
            ) ?? 'Some internal error occurred. Please try again later.',
            'status' => false,
            'error' => $e->getMessage()
        ], 400);
    }
}


}
