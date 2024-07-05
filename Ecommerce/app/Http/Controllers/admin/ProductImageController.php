<?php

namespace App\Http\Controllers\admin;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProductImageController extends Controller
{
    public function update(Request $request){
if ($request->image) {
    $image = $request->image;
    $ext = $image->getClientOriginalExtension();
    $sourcepath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();
        $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'-'.$ext;
        $productImage->image = $imageName;  
        $productImage->save();


        $thumbpath = public_path() . '/uploads/category/product/large/' . $imageName;
        $img = Image::make($sourcepath);
        $img->resize(1400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($thumbpath);

        //small
        $thumbpath = public_path() . '/uploads/category/product/small/' . $imageName;
        $img = Image::make($sourcepath);
        $img->fit(300, 300);
        $img->save($thumbpath);

        return response()->json([
            'status' => true, 
            'image_id' => $productImage->id,
            'imagepath' => asset('/uploads/category/product/small/' .$productImage->image),
            'message' => 'Image uploaded successfully']);
    } else {
        return response()->json(['status' => false, 'message' => 'No image uploaded.']);
    }
}

public function destroy(Request $request){
    $productImage = ProductImage::find($request->image_id);
    if(empty($productImage)){
        return response()->json([
            'status' => false,
            'message' => 'Image not found.'
        ]);
    }
    File::delete(public_path('/uploads/category/product/large/' . $productImage->image));
    File::delete(public_path('/uploads/category/product/small/' . $productImage->image));
    $productImage->delete();
    return response()->json([
        'status' => true,
         'message' => 'Image deleted successfully.'

        ]);
}

}