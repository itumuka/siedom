<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DosenController extends Controller
{
    public function index()
    {
        $title = 'Dashboard'; 
        $parent_breadcrumb = 'Dashboard';
        return view('dosen.dashboard', compact('title', 'parent_breadcrumb'));
    }

    public function kelas()
    {
        $title = 'Kelas'; 
        $parent_breadcrumb = 'Dashboard';
        return view('dosen.courses', compact('title', 'parent_breadcrumb'));
    }

 
    
}
