<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreRequest;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Utilities\ImageUploader;


class ProductsController extends Controller
{
    public function create()
    {
       
        $categories = Category::all();
        return view('admin.products.add',compact('categories'));
    }
    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();
       
        $admin = User::where('email','mostafa_gbhz@yahoo.com')->first();
        $createdProduct = Product::create([
            'title' => $validatedData['title'] ,
            'description' =>  $validatedData['description'] ,
            'category_id' =>  $validatedData['category_id'] ,
            'price' =>  $validatedData['price'] ,
            'owner_id' => $admin->id ,
        ]);
       try
       {
        $basePath = 'products/' . $createdProduct->id . '/' ;
        
        $images = [
            'thumbnail_url' => $validatedData['thumbnail_url'] ,
            'demo_url' => $validatedData['demo_url'] ,

        ];
        $imagesPath  = ImageUploader::uploadMany($images,$basePath);
        $sourceImageFullPath = $basePath . 'source_url_' . $validatedData['source_url']->getClientOriginalName();
        ImageUploader::upload($validatedData['source_url'],$sourceImageFullPath,'local_storage');

        $updatedProduct =  Product::where('id',$createdProduct->id)->update([
            'thumbnail_url' => $imagesPath['thumbnail_url'] ,
            'demo_url' => $imagesPath['demo_url'] ,
            'source_url' => $sourceImageFullPath

        ]);

        //dd($updatedProduct);

        if(!$updatedProduct)
        {
           throw new \Exception('تصاویر بارگزاری نشدند.');  
        }

        return back()->with('success','محصول ایجاد شد.');

       }catch(\Exception $e)
       {

        return back()->with('failed',$e->getMessage());
       
       }
       
        
      
    }

    public function all()
    {
        $products = Product::paginate(10);
        return view('admin.products.all',compact('products'));
    }

    public function downloadDemo($product_id)
    {
        $product = Product::findOrFail($product_id);

        return response()->download(public_path($product->demo_url));
    }
    public function downloadSource($product_id)
    {
        $product = Product::findOrFail($product_id);

        return response()->download(storage_path('app/local_storage/'.$product->source_url));
    }
}


