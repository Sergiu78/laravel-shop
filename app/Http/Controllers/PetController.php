<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function about()
    {
        return view('pet-shop/about-us');
    }
    public function shopPage()
    {
        $products = Product::get();
        return view('pet-shop/shop-page', compact('products'));
    }
}
