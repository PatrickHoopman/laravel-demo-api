<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = 'product';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'serial' => $this->product_id,
            'name' => $this->name,
            'brand' => $this->brand,
            'price' => $this->price,
            'stock' => $this->stock,
            'outOfStock' => $this->out_of_stock,
            'deleted' => $this->deleted,
        ];
    }
}
