<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'statistician':
                    return redirect()->route('statistician.dashboard');
                case 'provider':
                    return redirect()->route('provider.dashboard');
            }
        }
        
        return view('welcome');
    }
}
