<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\PaymentRequest;
use App\Mail\SendOrderedImages;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\Payment\PaymentService;
use App\Services\Payment\Requests\IDPayRequest;
use App\Services\Payment\Requests\IDPayVerifyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
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
                 
                if( count($orderItems) <= 0)
                {
                    throw new \InvalidArgumentException('سبد خرید شما خالیه.');
                }
                
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
                
                $createdPayment = Payment::create([
                    'gateway' => 'idpay',
                    'ref_code'  => $refCode ,
                    //'res_id'  => $refId ,
                    'status' => 'unpaid' ,
                    'order_id' => $createdOrder->id 
                ]);


                $idPayRequest = new IDPayRequest([
                     'amount' => $productsPrice ,
                     'user' => $user ,
                     'orderId' => $refCode  , 
                     //this api key is sample plz get it soon
                     'apiKey' => '12345678' , 
                 ]);
                $paymentService = new PaymentService(PaymentService::IDPAY,$idPayRequest);
                return $paymentService->pay() ;

            }catch(\Exception $e){
                return back()->with('failed', $e->getMessage());
                //dd($e->getMessage());
            }

        
    }

    public function callback(Request $request)
    {
        $paymentInfo = $request->all()  ;
        $idPayVerifyRequest = new IDPayVerifyRequest([
            'orderId'=> $paymentInfo['order_id']   ,
            'id'=> $paymentInfo['id']   ,
            'apiKey'=> '12345678'  ,
        ]);
        $paymentService = new PaymentService(PaymentService::IDPAY, $idPayVerifyRequest);
        $result =  $paymentService->verify() ;
        if(!$result['status'] )
        {
           return redirect()->route('home.checkout')->with('failed','پرداخت شما انجام نشد.') ;
        }



        if($request['status'] == 101 )
        {
           return redirect()->route('home.checkout')->with('failed','پرداخت شما قبلا انجام و تصاویر براتون ایمیل شده است .') ;
        }

        $currentPayment = Payment::where( 'ref_code' ,$result['data']['order_id'])->first();

        $currentPayment->update([
            'status' => 'paid' ,
            'res_id' => $result['data']['track_id']
        ]);
        $currentPayment->order()->update([
            'status' => 'paid' ,
        ]) ; 
        $currentUser = $currentPayment->order->user;
        $reservedImages =  $currentPayment->order->orderItems->map(function($orderItem){
          return  $orderItem->product->source_url ;
        });

        

        Mail::to($currentUser)->send(new SendOrderedImages($reservedImages->toArray(),$currentUser));
        Cookie::queue('basket',null);
        return redirect()->route('home.products.all')->with('success','خریدت انجام شد و تصاویر برات ارسال شد');

        
    }
}
