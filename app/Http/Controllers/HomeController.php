<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
        // Productos activos con su variante de mayor stock (más vendida/disponible)
        // ordenados de mayor a menor stock total de variantes
        $products = Producto::where('estado', 1)
            ->with(['variantes' => function ($q) {
                $q->orderByDesc('stock')->with('valores.atributo');
            }, 'categoria', 'atributos'])
            ->withSum('variantes', 'stock')
            ->orderByDesc('variantes_sum_stock')
            ->get();

        $categorias = \App\Models\Categoria::whereHas('productos', function($q) {
            $q->where('estado', 1);
        })->get();

        if (Auth::check() && Auth::user()->rol === 'vendedor') {
            return view('home-vendedor', compact('products', 'categorias'));
        }

        return view('home', compact('products', 'categorias'));
    }
}
