<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subCategories = SubCategory::latest('id');

        if(!empty($request->get("keyword"))){
            $subCategories = $subCategories->where("name","like", "%". $request->get("keyword") ."%");
        }
        $subCategories = $subCategories->paginate(10);
        return view("Admin.SubCategory.index", compact("subCategories"));

    }
    public function create()
    {
        $categories = Category::orderBy("name", "asc")->get();
        $data['categories'] = $categories;
        return view('Admin.SubCategory.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'status' => 'nullable',
            'category_id' => 'required',
        ]);

        if ($validator->passes()) {
            $inputs = $request->all();
            $SubCategory = SubCategory::create($inputs);

            // $SubCategory = new SubCategory();
            // $SubCategory->name = $request->name;
            // $SubCategory->slug = $request->slug;
            // $SubCategory->status = $request->status;
            // $SubCategory->category_id = $request->category_id;
            // $SubCategory->save();

            Session::flash('success', 'Sub-category added Successfully');
            if ($SubCategory) {
                return response()->json([
                    'status' => true,
                    'message' => 'Sub-category added successfuly',
                    'subcategory' => $SubCategory,
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Sub-category cannot be added',
                'errors' => $validator->errors(),
            ]);
        }
    }
}
