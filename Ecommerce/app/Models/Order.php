<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    //return orderitems
    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}
