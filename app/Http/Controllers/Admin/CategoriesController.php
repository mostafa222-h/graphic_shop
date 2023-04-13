<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\StoreRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function create()
    {
       return view('admin.categories.create');
    }
    public function store(StoreRequest $request)
    {
        $validatedData = $request->validate();
        $createdCategory = Category::create([
            'title' => $validatedData['title']  ,
            'slug' => $validatedData['slug']
        ]);
        if(!$createdCategory)
        {
            return back()->with('faild','.دسته بندی ایجاد نشد');
        }
        return back()->with('success','.دسته بندی ایجاد شد');
    }
}