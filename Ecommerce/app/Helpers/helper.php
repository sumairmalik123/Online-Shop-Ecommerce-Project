<?php
use App\Models\Category;
use App\Models\ProductImage;

function getCategories()
{
    $categories = Category::orderBy('name', 'asc')
    ->with('subCategories')
    ->orderBy('id', 'DESC')
    ->where('status', '1')
    ->where('showHome', 'Yes')
    ->get();
    return $categories;
}
function getProductImage($productId){
   return ProductImage::where('product_id',$productId)->first();
}
?>