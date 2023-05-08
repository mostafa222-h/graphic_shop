<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('passcreate',function(){
//     dd(bcrypt('12345678'));
// });
Route::prefix('admin')->group(function(){
    Route::prefix('categories')->group(function(){


        Route::get('',[CategoriesController::class,'all'])->name('admin.categories.all');
        Route::get('create',[CategoriesController::class,'create'])->name('admin.categories.create');
        Route::post('',[CategoriesController::class,'store'])->name('admin.categories.store');
        Route::delete('{category_id}/delete',[CategoriesController::class,'delete'])->name('admin.categories.delete');
        Route::get('{category_id}/edit',[CategoriesController::class,'edit'])->name('admin.categories.edit');
        Route::put('{category_id}/update',[CategoriesController::class,'update'])->name('admin.categories.update');


    });

    Route::prefix('products')->group(function(){

        Route::get('create',[ProductsController::class,'create'])->name('admin.products.create');
        Route::post('',[ProductsController::class,'store'])->name('admin.products.store');
        

    });
});
