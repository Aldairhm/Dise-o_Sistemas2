<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::all();
        return view('home', compact('products'));
    }

    public function variant()
    {
        $products = \App\Models\Product::all();
        return view('variant', compact('products'));
    }

    public function variantPremium()
    {
        $products = \App\Models\Product::all();
        return view('variant_premium', compact('products'));
    }

    public function variantApple()
    {
        $products = \App\Models\Product::all();
        return view('variant_apple', compact('products'));
    }

    public function inventario()
    {
        return view('productos');
    }
}
