<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::latest('id');
        if($request->get('keyword')){
            $keyword = $request->get('keyword');
            $query->where(function($q) use ($keyword){
                $q->where('name','like', "%{$keyword}%");
            });
        }
        $brands = $query->paginate(10);
        return view("Admin.Brand.index", compact("brands"));
    }
    public function create(Request $request)
    {
        return view("Admin.Brand.create");
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
            'status' => 'required',
        ]);
        if ($validator->passes()) {
            $inputs = $request->all();
            $brands = Brand::create($inputs);
            Session::flash('success', 'Brand created successfully');
            if($brands){
                return response()->json([
                    'status' => true,
                    'message' => 'Brand created successfully',
                    'brands' => $brands,
                ]);
            }
        }
        else {
            return response()->json([
                'status' => false,
                'message' => 'Brand cannot be created',
                'errors' => $validator->errors(),
            ]);
        }

    }
    public function edit(Request $request, $id)
    {
        $brands = Brand::find($id);
        if($brands)
        {
            return view('Admin.Brand.edit', compact('brands'));
        }else {
            Session::flash('error','Brand not Found');
            return redirect()->route('admin.brand.index');
        }
    }
    public function update(Request $request, $id)
    {
        $brands = Brand::find($id);
        if(empty($brands)){
            Session::flash('error','Brand not Found');
            return response()->json([
                'status'=> false,
                'not Found' => true,
                'message' => 'Brand not found',
                ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug, '.$brands->id.',id',
            'status' => 'required',
        ]);
        if ($validator->passes()) {
            $inputs = $request->all();
            $brands->update($inputs);
            Session::flash('success', 'Brand created successfully');
            if($brands){
                return response()->json([
                    'status' => true,
                    'message' => 'Brand created successfully',
                    'brands' => $brands,
                ]);
            }
        }
        else {
            return response()->json([
                'status' => false,
                'message' => 'Brand cannot be created',
                'errors' => $validator->errors(),
            ]);
        }

    }
    public function destroy(Request $request, $id)
    {
        $brands = Brand::find($id);
        if($brands)
        {
            $brands->delete();
            Session::flash('success', 'Brand deleted successfully');
            return  response()->json([
                'status' => true,
                'message' => 'Brand deleted successully',
            ]);
        }else {
            Session::flash('error','Brand not Found');
            return response()->json([
                'status' => false,
                'message' => 'Brand not found'
            ]);
        }
    }
}
