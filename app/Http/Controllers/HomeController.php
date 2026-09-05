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

        // Cargar vista según el rol del usuario autenticado
        if (Auth::check() && Auth::user()->rol === 'vendedor') {
            return view('home-vendedor', compact('products'));
        }

        return view('home', compact('products'));
    }
}
