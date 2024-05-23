<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\TempImage;
use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index(Request $request)
    {
    $products = Product::latest('id')->with('product_images');//with relationship hai
    if (($request->get('keyword') !== "")) {
        $products = $products->where('title', 'like', '%' . $request->get('keyword') . '%');
      }
    $products = $products->paginate(10);
    $data['products'] = $products;
    return view('product.list', $data);
        
    }
    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy    ('name', 'asc')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('product.create', $data);
    }

    public function store(Request $request){       
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
               'is_featured' => 'required|in:Yes,No',
               
        ];
        
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->passes()) {
        // Proceed with your logic if validation passes
        $product = new Product;
        $product->title = $request->title;
        $product->slug = $request->slug;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->track_qty = $request->track_qty;
        $product->qty = $request->qty;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->brand_id = $request->brand;
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;
        $product->save();


        //save product images
        if (!empty($request->product_images)) {
            foreach ($request->product_images as $temp_image_id) {
                $tempImageInfo = TempImage::find($temp_image_id);
                $textArray = explode('.', $tempImageInfo->name);
                $ext = last($textArray);
                $productImage = new ProductImage;
                $productImage->product_id = $product->id;
                $productImage->image = 'NULL'; 
                $productImage->save();
    
                // Image name
                $newName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                $productImage->image = $newName;
                $productImage->save();
    
                // Generate and save large thumbnail
                //large image
                $sourcepath = public_path() . '/temp/' . $tempImageInfo->name;
                $thumbpath = public_path() . '/uploads/category/product/large/' . $newName;
                $img = Image::make($sourcepath);
                $img->resize(1400, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save($thumbpath);
    
                // Generate and save small thumbnail
                $thumbpath = public_path() . '/uploads/category/product/small/' . $newName;
                $img = Image::make($sourcepath);
                $img->fit(300, 300);
                $img->save($thumbpath);
            }
        }
            
        $request->session()->flash('success','Product added successfully');
        return response()->json([
            'status' => true,
            'message' => 'Product added successfully'
        ]);
        } else {
            // Handle validation errors
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        
    }
    public function edit($id, Request $request)
    {
        //dd($request->id);
        //exit();
        // Find the product by ID
        $product = Product::find($id);
        //dd($product);
        //exit();
    
        // Check if the product exists
       if (empty($product)) {
        //session messgae
        $request->session()->flash('error', 'Product not found');
           // Redirect to a failed page or display an error message
           return redirect()->route('product.list')->with('error', 'Product not found');

             //Handle the case when the product is not found
          //abort(404);
       }
    
        // Retrieve subcategories based on the product's category ID
        $productImages = ProductImage::where('product_id', $product->id)->get();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
    
        return view('product.edit', compact('product', 'categories', 'subcategories', 'brands', 'productImages'));
    }
    

public function update($id, Request $request)
{
    $product = Product::find($id);
    $rules = [
        'title' => 'required',
        'slug' => 'required|unique:products,slug,'.$product->id.',id', 
        'price' => 'required|numeric',
        'sku' => 'required|unique:products,sku,'.$product->id.',id',
        'track_qty' => 'required|in:Yes,No',
        'category' => 'required|numeric',
           'is_featured' => 'required|in:Yes,No',
           
    ];
    
    if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
        $rules['qty'] = 'required|numeric';
    }
    
    $validator = Validator::make($request->all(), $rules);
    
    if ($validator->passes()) {
    // Proceed with your logic if validation passes
    $product->title = $request->title;
    $product->slug = $request->slug;
    $product->description = $request->description;
    $product->price = $request->price;
    $product->compare_price = $request->compare_price;
    $product->sku = $request->sku;
    $product->barcode = $request->barcode;
    $product->track_qty = $request->track_qty;
    $product->qty = $request->qty;
    $product->category_id = $request->category;
    $product->sub_category_id = $request->sub_category;
    $product->brand_id = $request->brand;
    $product->status = $request->status;
    $product->is_featured = $request->is_featured;
    $product->save();


    //session flash message
    $request->session()->flash('success','Product update successfully');
    return response()->json([
        'status' => true,
        'message' => 'Product update successfully'
    ]);
    } else {
        // Handle validation errors
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}


public function destroy($id, Request $request)
{
    $product = Product::find($id);
    $request->session()->flash('error','Product not found');
    if(empty($product)){
        return response()->json([
            'status' => false,
            'notfound' => true,
            'message' => 'Product not found.'
        ]);
    }
    $productImages = ProductImage::where('product_id', $id)->get();
    if(!empty($productImages)){
        foreach($productImages as $productImage){
            File::delete(public_path('/uploads/category/product/large/' . $productImage->image));
            File::delete(public_path('/uploads/category/product/small/' . $productImage->image));
        }
        $productImage::where('product_id', $id)->delete();
    }
    $product->delete();
    $request->session()->flash('success','Product deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully.'
        ]);
}

}