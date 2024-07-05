<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    public function index(Request $request) {
      $categorys = Category::latest();
      if (!empty($request->get('keyword'))) {
        $categorys = $categorys->where('name', 'like', '%' . $request->get('keyword') . '%');
      }
      $categorys = $categorys->paginate(10);
      return view('layouts/listcategory',compact('categorys'));     
    }
    public function create() {
        return view('category.create');
    }
    public function store(Request $request) {
      $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:categories',
      ]);  
      if ($validator->passes()) {
        $category = new Category;
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->showHome = $request->showHome;
        $category->save();


        //image save
        if ($request->has('image_id')) {
          $tempImage = TempImage::find($request->image_id);
          $extArray = explode('.', $tempImage->name);
          $ext = last($extArray);
          $newImageName = $category->id. '-'. time(). '.'. $ext;
          $spath = public_path('temp/'. $tempImage->name);
          $dpath = public_path('uploads/category/'. $newImageName);
          File::copy($spath, $dpath);
          
          // Save thumbnail
          $thumbPath = public_path('uploads/category/thumb/'. $newImageName);
          $img = Image::make($spath);
          $img->resize(450, 600);
          $img->save($thumbPath);
          $category->image = $newImageName;
         $category->save();
      }
        

        $request->session()->flash('success', 'Category created successfully');
        return response()->json([
          'status' => true,
          'message' => 'Category created successfully',
        ]);
        
    } else {
        return response()->json([
            'status' => false, 
            'errors' => $validator->errors()
          ]);
    }

    }
    public function edit($categoryID, Request $request) {
        $category = Category::find($categoryID);
        if(empty($category)) {
            return redirect()->route('categorys.index')->with('error', 'Category not found');
        }




        return view('category.edit',compact('category'));
    }
    public function update($categoryID, Request $request) {
      $category = Category::find($categoryID);
      if(empty($category)) {
        return response()->json([
          'status' => false,
          'notFound' => true,
          'message' => 'Category not found',
        ]);
      }
      $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:categories,slug,' . $category->id . ',id',
      ]);  
      if ($validator->passes()) {
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->showHome = $request->showHome;
        $category->save();

         //oldimage
         $oldimage = "";
         $oldimage = $category->image; //category table ma sa image ko get kia hai or osko oldimage ma store kia hai
        //image upload
        $tempImage = TempImage::find($request->image_id); // Humne jo id pass ki hai wo TempImage se dekhenge
        $tempImageName = $tempImage? $tempImage->name : null; // Temporary image ka naam
        $extArray = explode('.', $tempImageName);
        $ext = last($extArray);
        $newimagename = $category->id .'- '. time() .'.'. $ext;
        $spath = public_path('temp/' . $tempImageName); // Image kahan store hai
        $dpath = public_path('uploads/category/' . $newimagename); // Destination path
        if ($tempImageName && file_exists($spath)) {
            File::copy($spath, $dpath);
            $category->image = $newimagename;
            $category->save();


            //delete old image
            $oldimage = "";
            File::delete(public_path().'/uploads/category/' . $oldimage);//delete old image
            File::delete(public_path(). '/uploads/category/thumb/' . $oldimage);//delete old image
        }
        

        $request->session()->flash('success', 'Category updated successfully');
        return response()->json([
          'status' => true,
          'message' => 'Category updated successfully',
        ]);
        
    } else {
        return response()->json([
            'status' => false, 
            'errors' => $validator->errors()
          ]);
    }

        
    }
    public function destroy($categoryID, Request $request) {
        $category = Category::find($categoryID);
        if(empty($category)) {
            $request->session()->flash('error', 'Category not found');
            return response()->json([
              'status' => false,
              'message' => 'Category not found',
            ]);
        }
        $oldimage = "";
        File::delete(public_path().'/uploads/category/' . $oldimage);//delete old image
        File::delete(public_path(). '/uploads/category/thumbs/' . $oldimage);//delete old image
        $category->delete();
        $request->session()->flash('success', 'Category deleted successfully');
        return response()->json([
          'status' => true,
          'message' => 'Category deleted successfully',
        ]);
        
    }
}
