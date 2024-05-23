<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request) {
        $brands = Brand::latest('id');
        if ($request->get('keyword')) {
            $brands = $brands->where('name', 'like', '%' . $request->get('keyword') . '%');
        }       
        $brands = $brands->paginate(10);
        return view('brands.list', compact('brands'));
    }

    public function create() {
        return view ('brands.brand');
}

public function store(Request $request) {
   $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required|unique:brands',
    ]);
    if ($validator->passes()) {
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->save();
        return response()->json([
            'status' => true,
            'success' => 'Brand created successfully.']);
    } else {
        return response()->json([ 
            'status' => false,  
         'error' => $validator->errors()]);
    }
}

public function edit($id, Request $request) {
    $brand = Brand::find($id);
    if (empty($brand)){
        $request->session()->flash('error', 'Brand not found');
        return redirect()->route('brands.list');
    }
    return view('brands.edit', compact('brand'));
}

public function update($id, Request $request) {
    $brand = Brand::find($id);
    if (empty($brand)){
        $request->session()->flash('error', 'Brand not found');
        return response ()->json([
            'status' => false,
            'message' => 'Brand not found',
            'notfound' => true
        ]);
    }
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,' .$brand->id . ',id',
    ]);
    if ($validator->passes()) {
        $brand = Brand::find($id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->save();
        return response()->json([
            'status' => true,
            'success' => 'Brand updated successfully.']);
    } else {
        return response()->json([ 
            'status' => false,  
         'error' => $validator->errors()]);
    }
}

public function destroy($id, Request $request) {
    $brand = Brand::find($id);
    if (empty($brand)){
        $request->session()->flash('error', 'Brand not found');
        return response ()->json([
            'status' => false,
            'message' => 'Brand not found',
            'notfound' => true
        ]);
    }
    $brand->delete();
    return response()->json([
        'status' => true,
        'message' => 'Brand deleted successfully.']);


}
}