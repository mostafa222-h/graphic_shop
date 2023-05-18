<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\PaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\Payment\PaymentService;
use App\Services\Payment\Requests\IDPayRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function pay(PaymentRequest $request)
    {
        //dd("aaa");
        $validatedData = $request->validated();
        $user = User::firstOrCreate(
            [ 'email' =>$validatedData['email']],
            [

            'name' => $validatedData['name'] ,
            'mobile' =>$validatedData['mobile'] 
           
            ]);
           
            try{

                $orderItems = json_decode(Cookie::get('basket'),true);
                
                $products = Product::findMany(array_keys($orderItems));
                $productsPrice = $products->sum('price');
                
                $refCode = Str::random(30) ;
                $createdOrder = Order::create([
                   'amount' => $productsPrice,
                   'ref_code' => $refCode,
                   'status' => 'unpaid' ,
                   'user_id' => $user->id ,
                ]);
                $orderItemsForCreatedOrder = $products->map(function($product){
                   $currentProduct = $product->only(['price','id']) ;
                   $currentProduct['product_id']  = $currentProduct['id']  ;
                   unset($currentProduct['id']);
                   return $currentProduct;
                });
               
                $createdOrder->orderItems()->createMany( $orderItemsForCreatedOrder->toArray());
                $refId = rand(1111,9999) ;
                $createdPayment = Payment::create([
                    'gateway' => 'idpay',
                    'ref_id'  => $refId ,
                    'res_id'  => $refId ,
                    'status' => 'unpaid' ,
                    'order_id' => $createdOrder->id 
                ]);


                $idPayRequest = new IDPayRequest([
                     'amount' => $productsPrice ,
                     'user' => $user ,
                     'orderId' => $refCode  ,  
                 ]);
                $paymentService = new PaymentService(PaymentService::IDPAY,$idPayRequest);
                return $paymentService->pay() ;

            }catch(\Exception $e){
                return back()->with('failed', $e->getMessage());
                //dd($e->getMessage());
            }

        
    }

    public function callback()
    {
        
    }
}