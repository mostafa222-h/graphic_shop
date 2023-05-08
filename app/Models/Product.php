<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['title','description','price','category_id','owner_id'];
    protected $guareded = [] ;
    public function getFilePath()
    {
        return 'products'  ;
    }
}
