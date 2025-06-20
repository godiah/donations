<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    // Display all applications
    public function index()
    {
        return view('admin.applications.index');
    }
}
