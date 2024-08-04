<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TempImagesController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('phpinfo', function () {
    return phpinfo();
});


Route::group(['prefix' => 'admin'], function ()
{
    Route::group(['middleware' => 'admin.guest'], function(){

        Route::get('/login', [AdminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class,'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function(){

        Route::get('/dashboard', [HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class,'logout'])->name('admin.logout');

        //For Category
        Route::get('/category', [CategoryController::class,'index'])->name('admin.category.index');
        Route::get('/category/create', [CategoryController::class,'create'])->name('admin.category.create');
        Route::post('/category/store', [CategoryController::class,'store'])->name('admin.category.store');
        Route::get('/category/{category}/edit', [CategoryController::class,'edit'])->name('admin.category.edit');
        Route::put('/category/{category}', [CategoryController::class,'update'])->name('admin.category.update');
        Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');

        Route::post('/upload-temp-image', [TempImagesController::class,'create'])->name('temp-images.create');

        //For SubCategory
        Route::get('/sub-category', [SubCategoryController::class, 'index'])->name('admin.sub-category.index');
        Route::get('/sub-category/create', [SubCategoryController::class, 'create'])->name('admin.sub-category.create');
        Route::post('/sub-category/store', [SubCategoryController::class, 'store'])->name('admin.sub-category.store');
   

        Route::get('/getslug', function(Request $request){
            $slug = '';
            if(!empty($request->title)){
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug,
            ]);
        })->name('getslug');

    });
});
