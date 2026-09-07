<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
        $products = Producto::all();

        if (Auth::check() && Auth::user()->rol === 'vendedor') {
            return view('home-vendedor', compact('products'));
        }

        return view('home', compact('products'));
    }
}
