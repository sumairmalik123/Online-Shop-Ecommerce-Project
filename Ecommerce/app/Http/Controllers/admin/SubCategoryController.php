<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request){
        $subcategorys = SubCategory::latest('sub_categories.id')
        ->leftJoin('categories', 'categories.id', 'sub_categories.category_id')
        ->select('sub_categories.*', 'categories.name as categoryname');
        if (!empty($request->get('keyword'))) {
          $subcategorys = $subcategorys->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
          $subcategorys = $subcategorys->orWhere('categories.name', 'like', '%' . $request->get('keyword') . '%');

        }
        $subcategorys = $subcategorys->paginate(10);
        return view('subcategory.list',compact('subcategorys')); 
    }

    public function create(){
        $categories = Category::orderby('name','asc')->get();
        $data['categories'] = $categories;
        return view('subcategory.create',$data);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required',
          ]); 

        if ($validator->passes()) {
            $subcategories = new SubCategory;
            $subcategories->name = $request->name;
            $subcategories->slug = $request->slug;
            $subcategories->category_id = $request->category;
            $subcategories->status = $request->status;
            $subcategories->showHome = $request->showHome;
            $subcategories->save();
            $request->session()->flash('success', 'Subcategory created successfully');
            return response()->json([
                'status' => true,
                'message' => 'Subcategory created successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
       
    }

    public function edit($id, Request $request){
        $subcategory = SubCategory::find($id);
        if (empty($subcategory)) {
            $request->session()->flash('error', 'Subcategory not found');
            return redirect()->route('subcategories.list');
        }
        $categories = Category::orderby('name','asc')->get();
        $data['subcategory'] = $subcategory;
        $data['categories'] = $categories;
        return view('subcategory.edit',$data);
}

    public function update(Request $request, $id){
        $subcategory = SubCategory::find($id);
        if (empty($subcategory)) {
            $request->session()->flash('error', 'Subcategory not found');
            return resonse ()->json([
                'status' => false,
                'notfound' => true,
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subcategory->id.',id',
            'category' => 'required',
            'status' => 'required'
        ]);
        
        if ($validator->passes()) {
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->category_id = $request->category;
            $subcategory->status = $request->status;
            $subcategory->showHome = $request->showHome;
            $subcategory->save();
            $request->session()->flash('success', 'Subcategory updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Subcategory updated successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
       
}

    public function destroy($id, Request $request){
        $subcategory = SubCategory::find($id);
        if (empty($subcategory)) {
            $request->session()->flash('error', 'Subcategory not found');
            return response ()->json([
                'status' => false,
                'notfound' => true,
            ]);
        }
        $subcategory->delete();
        $request->session()->flash('success', 'Subcategory deleted successfully');
        return response ()->json([
            'status' => true,
            'message' => 'Subcategory deleted successfully',
        ]);
    }
}