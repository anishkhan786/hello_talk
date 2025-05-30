<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\contry;
use App\Models\languag;
use Illuminate\Http\Request;

class ContryController extends Controller
{
    public function contry(){
        $data = contry::all();
        return response()->json([
            'contry'=> $data,
            'status'=> true,
            'code'=>'200'
        ]);
    }

    public function language(){
        $data = languag::all();
        return response()->json([
            'language'=> $data,
            'status'=> true,
            'code'=>'200'
        ]);
    }
}
