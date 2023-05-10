<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\User;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['title','description','price','category_id','owner_id'];
    protected $guareded = [] ;
    public function getFilePath()
    {
        return 'products'  ;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class,'owner_id');
    }
}
