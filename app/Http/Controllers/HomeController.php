<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

public function privacy_policy_page()
{
    return view('/privacy-policy');
}



}
