<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentLoginController extends Controller
{
    public function index(): View
    {
        return view('student.loginpage');
    }
}
