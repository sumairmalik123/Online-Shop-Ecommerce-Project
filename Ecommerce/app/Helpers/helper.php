<?php
use App\Models\Category;

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
?>