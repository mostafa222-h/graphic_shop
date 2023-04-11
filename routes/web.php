<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user',[UserController::class,'index']);

Route::get('create/category', function () {
   $created_category =  Category::find(1)->update([
    'title' => 'titlethree',
    'slug' => 'title-three'
   ]);
    dd($created_category);
});