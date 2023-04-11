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

//Route::get('/user',[UserController::class,'index']);

Route::get('products/all', function () {
    return view('frontend.products.all');
});

Route::get('admin_panel', function () {
    return view('admin.index');
});

Route::get('admin_users', function () {
    return view('admin.users.index');
});