<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $incrementing = false;
    protected $appends = ['out_of_stock', 'deleted'];
    protected $keyType = 'uuid';
    protected $fillable = [
        'product_id',
        'name',
        'brand',
        'price',
        'stock',
    ];
    public function getOutOfStockAttribute()
    {
        return $this->stock <= 0;
    }
    public function getDeletedAttribute()
    {
        if(!is_null($this->deleted_at)) {
            return true;
        } else {
            return false;
        }
    }
}
