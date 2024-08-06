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
        $query = SubCategory::with('category')
            ->select('sub_categories.*', 'categories.name as categoryName')
            ->latest('sub_categories.id')
            ->leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id');

        if ($request->get("keyword")) {
            $keyword = $request->get("keyword");
            $query->where(function ($query) use ($keyword) {
                $query->where('sub_categories.name', 'like', "%{$keyword}%")
                      ->orWhere("categories.name", "like", "%{$keyword}%");
            });
        }
        $subCategories = $query->paginate(10);
        return view('Admin.SubCategory.index', compact('subCategories'));
    }
    public function create()
    {
        $categories =  Category::orderBy('name', 'asc')->get();
        return view('Admin.SubCategory.create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'status' => 'required',
            'category_id' => 'required',
        ]);

        if ($validator->passes()) {
            // $inputs = $request->all();
            // $subCategory = SubCategory::create($inputs);

            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category_id;
            $subCategory->save();

            Session::flash('success', 'Sub-Category created successfully');
            if($subCategory){
                return response()->json([
                    'status' => true,
                    'message' => 'Sub-Category created successfully',
                    'subCategory' => $subCategory,
                ]);
            }
        }
        else {
            return response()->json([
                'status' => false,
                'message' => 'Sub-Category cannot be created',
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        if($subCategory)
        {
            $categories = Category::orderBy('name','asc')->get();
            return view('Admin.SubCategory.edit', [
                'categories'=> $categories,
                'subCategory' => $subCategory,
            ]);
        }
        else{
            Session::flash('error','Sub-Category not found ');
            return redirect()->route('admin.sub-category.index');
        }
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            Session::flash('error','Sub-Category not Found');
            return response()->json([
                'status'=> false,
                'not Found' => true,
                'message' => 'Sub-Category not found',
                ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug, '.$subCategory->id.',id',
            'status' => 'required',
            'category_id' => 'required',
        ]);
        if ($validator->passes()) {
            $inputs = $request->all();
            $subCategory->update($inputs);

            // $subCategory = new SubCategory();
            // $subCategory->name = $request->name;
            // $subCategory->slug = $request->slug;
            // $subCategory->status = $request->status;
            // $subCategory->category_id = $request->category;
            // $subCategory->save();

            Session::flash('success', 'Sub-Category updated successfully');
            if($subCategory){
                return response()->json([
                    'status' => true,
                    'message' => 'Sub-Category updated successfully',
                    'subCategory' => $subCategory,
                ]);
            }
        }
        else {
            return response()->json([
                'status' => false,
                'message' => 'Sub-Category cannot be updated',
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        if ($subCategory) {
            $subCategory->delete();
            Session::flash('success', 'Sub-Category deleted successfully');
            return  response()->json([
                'status' => true,
                'message' => 'Sub-Category deleted successully',
            ]);
        } 
        else {
            Session::flash('error','Sub-Category not Found');
            return response()->json([
                'status' => false,
                'message' => 'Sub-Category not found'
            ]);
        }
    }
}
