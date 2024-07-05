<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class TempImagesController extends Controller
{

    public function create(Request $request)
    {
        $image = $request->image; // File object from the request

        if (!empty($image) && $image->isValid()) {
            $ext = $image->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $sourcepath = public_path('temp/' . $newName); // Absolute path to uploaded file

            // Move the uploaded file to the temp directory
            if ($image->move(public_path('temp'), $newName)) {
                // File uploaded successfully
                $thumbpath = public_path('temp/thumb/' . $newName);

                try {
                    $img = Image::make($sourcepath);
                    $img->fit(300, 275);
                    $img->save($thumbpath);

                    return response()->json([
                        'status' => true,
                        'image_id' => $tempImage->id,
                        'imagepath' => asset('/temp/thumb/' . $newName),
                        'message' => 'Image uploaded successfully',
                    ]);
                } catch (Intervention\Image\Exception\NotReadableException $e) {
                    // Handle "Image source not readable" exception
                    return response()->json([
                        'status' => false,
                        'message' => 'Error processing image: ' . $e->getMessage(),
                    ], 500); // Internal Server Error
                }
            } else {
                // Error moving the uploaded file
                return response()->json([
                    'status' => false,
                    'message' => 'Error uploading image.',
                ]);
            }
        } else {
            // No image uploaded or invalid file
            return response()->json([
                'status' => false,
                'message' => 'No image uploaded or invalid file.',
            ]);
        }
    }
}