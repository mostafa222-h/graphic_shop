<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\AbstractProviderInterface;
use App\Services\Payment\Contracts\PayableInterface;
use App\Services\Payment\Contracts\RequestInterface;
use App\Services\Payment\Contracts\VerifaibleInterface;

class ZarinpalProvider extends AbstractProviderInterface implements PayableInterface , VerifaibleInterface
{
    
   
    public function pay()
    {
        
    }
    public function verify()
    {
        
    }
}