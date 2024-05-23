<?php

namespace App\Http\Controllers\admin;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductSubController extends Controller
{
    public function index(Request $request)
    {
        if (!empty($request->category_id)) {
            $subCategories = SubCategory::where('category_id', $request->category_id)
     ->orderBy('name', 'asc')->get();
        return response ()->json([
            'status' => true,
            'subCategories' => $subCategories
        ]); 
        } else {
            return response ()->json([
                'status' => false,
                'subCategories' => []
            ]);
        }
    }
}
