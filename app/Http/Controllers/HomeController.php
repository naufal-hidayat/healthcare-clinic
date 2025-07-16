<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)
            ->orderBy('category')
            ->get()
            ->groupBy('category');
            
        $doctors = Doctor::where('is_active', true)
            ->orderBy('specialization')
            ->get();

        return view('welcome', compact('services', 'doctors'));
    }
}