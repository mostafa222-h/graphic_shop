<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Http\Requests\Admin\Categories\StoreRequest;
use App\Http\Requests\Admin\Categories\UpdateRequest;
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
        //dd($validatedData);
        $createdCategory = Category::create([
            // 'title' => $request->title  ,
            // 'slug' => $request->slug
            'title' => $validatedData['title']  ,
            'slug' => $validatedData['slug']
        ]);
        if(!$createdCategory)
        {
            return back()->with('faild','.دسته بندی ایجاد نشد');
        }
        return back()->with('success','.دسته بندی ایجاد شد');
    }
    public function all()
    {
       
        $categories = Category::paginate(10);
        //dd($categories);
        return view('admin.categories.all',compact('categories'));
    }

    public function delete($category_id)
    {
        $category = Category::find($category_id);
        $category->delete();
        return back()->with('success','دسته بندی حذف شد');
    }
    public function edit($category_id)
    {
        $category = Category::find($category_id);
        return view('admin.categories.edit',compact('category'));
       // $category->update();

    }
    public function update(UpdateRequest $request , $category_id)
    {
        $validatedData = $request->validated();
        $category = Category::find($category_id);
        $result = $category->update([
            'title' => $validatedData['title'],
            'slug' => $validatedData['slug']
        ]);
        if(!$result)
        {
            return back()->with('failed','به روز رسانی نشد');
        }
        return back()->with('success','به روز رسانی شد');
    }
}
