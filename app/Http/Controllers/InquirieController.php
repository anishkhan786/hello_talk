<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Inquiries;

class InquirieController extends Controller
{
    public function index()
    {
        $data = Inquiries::with('users')->paginate(10);
        return view('admin/inquirie',compact('data'));
    }
}
