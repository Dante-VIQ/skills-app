<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function terms()
    {
        return view('legal.terms');
    }
    
    public function privacy()
    {
        return view('legal.privacy');
    }
    
    public function acceptableUse()
    {
        return view('legal.acceptable-use');
    }
    
    public function cookiePolicy()
    {
        return view('legal.cookie-policy');
    }
}