<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(){
    $categories = Category::orderBy('name', 'ASC')->with('subCategories')->where('status',1)->get();
    $brands = Brand::orderBy('name', 'ASC')->where('status',1)->get();
    $products = Product::orderBy('id', 'DESC')->where('status',1)->get();

    $data['categories'] = $categories;
    $data['brands'] = $brands;
    $data['products'] = $products;

        return view('Front.shop', $data);
    }
}
