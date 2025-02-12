<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;

        if(!empty($image))
        {
            $ext = $image->getClientOriginalExtension();
            $newName = time().".".$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path("/temp"), $newName);

            return response()->json([
                'status' => true,
                'message'=> 'Image Uploaded Successfully',
                'image_id' => $tempImage->id,
            ]);
        }

    }
}
