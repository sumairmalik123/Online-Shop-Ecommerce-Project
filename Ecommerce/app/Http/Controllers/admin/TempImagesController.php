<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

    class TempImagesController extends Controller
{

    public function create(Request $request) {
        $image = $request->image; // file sa jo image ka parameter hai osko access kia hai
        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension(); // image ka extension nikalny ka tareeqa
           $newName = time() . '.' . $ext; // file ka naya name
           $tempImage = new TempImage(); // model ke object banany ka tareeqa means table name 
           $tempImage->name = $newName; // yaha pr name column ka name hai
           $tempImage->save();// yaha pr save karny ka tareeqa
           $image->move(public_path().'/temp',$newName); // yaha pr image move karny ka tareeqa or /temp folder ka name hai 
           // or ya uper waly uper waly image move karny ka tareeqa jo request kia hai      

               //image thumbnail
               //$sourcepath = public_path().'/temp' . $newName;
               //$thumbpath = public_path().'/temp/thumb/' . $newName;
               //$img = Image::make($sourcepath);
               //$img->fit(450, 600);
              // $img->save($thumbpath);

               
           return response()->json([
               'status' => true,
               'image_id' => $tempImage->id,
               'imagepath' => asset('/temp/thumb/' .$newName),
               'message' => 'Image uploaded successfully'
           ]);
    }
}

}