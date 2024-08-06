<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest('id');
        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $categories = $categories->paginate(10);
        return view('Admin.Category.index', compact('categories'));
    }

    public function create()
    {
        return view('Admin.Category.create');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
            'status' => 'nullable',
            'image_id' => 'nullable|exists:temp_images,id',
        ]);

        if ($validator->passes()) {
            $inputs = $request->only(['name', 'slug', 'status']);
            $category = Category::create($inputs);
            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '.' . $ext;

                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);
            }

            //Generating Thumbnail
            $dPath = public_path() . '/uploads/category/thumb/' . $newImageName;
            $image = ImageManager::imagick()->read($sPath);
            $image->scale(450, 600);
            // $image->resize(450, 600);
            $image->save($dPath);


            $category->image = $newImageName;
            $category->save();

            Session::flash('success', 'Category Added Successfully');
            if ($category) {
                return response()->json([
                    'status' => true,
                    'message' => 'Category Added Successfully',
                    'category' => $category,
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Category cannot be created.',
                'errors' => $validator->errors(),
            ]);
        }
    }
    public function edit(Request $request, $id)
    {
        $category = Category::find($id);
        if ($category) {
            return view('Admin.Category.edit', compact('category'));
        } else {
            return redirect()->route('admin.category.index');
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (empty($category)) {
            Session::flash('error', 'Category not Found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not Found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id .',id',
            'status' => 'nullable',
            // 'image_id' => 'nullable|exists:temp_images,id',
        ]);

        if ($validator->passes()) {
            $inputs = $request->only(['name', 'slug', 'status']);
            $category->update($inputs);
            $oldImage = $category->image;
            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);
                //Generating Thumbnail
                $dPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $image = ImageManager::imagick()->read($sPath);
                $image->scale(450, 600);
                // $image->resize(450, 600);
                $image->save($dPath);
                $category->image = $newImageName;
                $category->save();
            }

            //Delete Old Image
            File::delete(public_path() . '/uploads/category/thumb/' . $oldImage);
            File::delete(public_path() . '/uploads/category/' . $oldImage);

            Session::flash('success', 'Category Updated Successfully');
            if ($category) {
                return response()->json([
                    'status' => true,
                    'message' => 'Category Updated Successfully',
                    'category' => $category,
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Category cannot be updated',
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::find($id);
        if($category)
        {
            $category->delete();
            File::delete(public_path() . '/uploads/category/thumb/' . $category->image);
            File::delete(public_path() . '/uploads/category/' . $category->image);
            Session::flash('success', 'Category deleted successfully.');
            return response()->json([
                'status'=> true,
                'message' => 'Category deleted successfully',
            ]);
        }
        else{
            Session::flash('error', 'Category not Found.');
            return response()->json([
                'status'=> true,
                'message' => 'Category not Found',
            ]);
            // return redirect()->route('admin.category.index');
        }
    }
}
