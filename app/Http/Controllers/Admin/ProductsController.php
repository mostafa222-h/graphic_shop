<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreRequest;
use App\Http\Requests\Admin\Products\UpdateRequest;
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


        if(!$this->uploadImages($createdProduct,$validatedData) ) 
        {
         return back()->with('failed','محصول ایجاد نشد.');
        }
        return back()->with('success',' محصول ایجاد شد. ' );
     
        
      
    }

    public function all()
    {
        $products = Product::paginate(10);
        return view('admin.products.all',compact('products'));
    }
    public function delete($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->delete();
        return back()->with('success' , 'محصول حذف شد.');
        
    }

    public function edit($product_id)
    {
        $categories = Category::all();
        $product = Product::find($product_id);

        return view('admin.products.edit',compact('product','categories'));
       

    }
    public function update(UpdateRequest $request , $product_id)
    {
        $validatedData = $request->validated();
        $product = Product::findOrFail($product_id);
        $result =$product->update([
            'title' => $validatedData['title'] ,
            'description' =>  $validatedData['description'] ,
            'category_id' =>  $validatedData['category_id'] ,
            'price' =>  $validatedData['price'] ,
        ]);
       
       if(!$this->uploadImages($product,$validatedData) or !$result) 
       {
        return back()->with('failed','تصاویر به روز رسان نشدند.');
       }
       return back()->with('success',' محصول به روز رسانی' );
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


    private function uploadImages($createdProduct,$validatedData)
    {
       
        try
        {
            $basePath = 'products/' . $createdProduct->id . '/' ;
            $sourceImageFullPath = null ;
            
            $data = [];
            if(isset($validatedData['source_url']))
            {
                $sourceImageFullPath = $basePath . 'source_url_' . $validatedData['source_url']->getClientOriginalName();
                ImageUploader::upload($validatedData['source_url'],$sourceImageFullPath,'local_storage');
                $data += ['source_url' => $sourceImageFullPath] ;
            }
            if(isset($validatedData['thumbnail_url']))
            {
                $fullPath = $basePath . 'thumbnail_url' . '_' . $validatedData['thumbnail_url']->getClientOriginalName();  ;
                ImageUploader::upload($validatedData['thumbnail_url'],$fullPath,'public_storage');
                $data += ['thumbnail_url' => $fullPath] ;
            }

            if(isset($validatedData['demo_url']))
            {
               
                $fullPath = $basePath . 'demo_url' . '_' . $validatedData['demo_url']->getClientOriginalName();  ;
                ImageUploader::upload($validatedData['demo_url'],$fullPath,'public_storage');
                $data += ['demo_url' => $fullPath] ;
            }
        
         $updatedProduct =  Product::where('id',$createdProduct->id)->update($data);
         if(!$updatedProduct)
         {
            throw new \Exception('تصاویر بارگزاری نشدند.');  
         }
         return true ;
         return back()->with('success','محصول ایجاد شد.');
 
        }catch(\Exception $e)
        {
            return false ;
         
        }
        
    }
}


