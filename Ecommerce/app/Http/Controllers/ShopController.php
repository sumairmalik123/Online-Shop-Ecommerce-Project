<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subcategorySelected = '';
        $brandsArray = [];

        $categories = Category::orderBy('name', 'ASC')->with('subCategories')->where('status', 1)->get();
        $brands = Brand::orderBy('name', 'ASC')->where('status', 1)->get();

        $products = Product::where('status', 1);

        // Apply filter
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $products = $products->where('category_id', $category->id);
                $categorySelected = $category->id;
            } else {
                echo '<p class="alert alert-danger">No category found with the slug "' . htmlspecialchars($categorySlug) . '".</p>';
            }
        }
       

        if (!empty($subCategorySlug)) {
            $subcategory = SubCategory::where('slug', $subCategorySlug)->first();
            if ($subcategory) {
                $products = $products->where('sub_category_id', $subcategory->id);
                $subcategorySelected = $subcategory->id;
            } else {
               // echo '<p class="alert alert-danger">No subcategory found with the slug "' . htmlspecialchars($subCategorySlug) . '".</p>';
               abort(404);
            }
        }
         if (!empty($request->get('brand'))){ 
             $brandsArray = explode(',',$request->get('brand'));
             $products = $products->whereIn('brand_id',$brandsArray); 
             }
       

        if ($request->get('price_max') != '' && $request->get('price_min') != '') {
            if ($request->get('price_max') == 1000){
                $products = $products->whereBetween('price', [intval($request->get('price_min')) , 10000]);

            }
            $products = $products->whereBetween('price', [intval($request->get('price_min')) , intval($request->get('price_max'))]);
        }

        //$products = $products->orderBy('id', 'DESC');
        if ($request->get('sort') != '') {
            if ($request->get('sort') == 'latest') {
                $products = $products->orderBy('id', 'DESC');
            } else if ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('price', 'ASC');
            } else {
                $products = $products->orderBy('price', 'DESC');
            }
        } else {
            $products = $products->orderBy('id', 'DESC');
        }
    
        $products = $products->paginate(6);

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subcategorySelected'] = $subcategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['price_min'] = (intval($request->get('price_min')));
        $data['price_max'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $data['sort'] = $request->get('sort');

        return view('Front.shop', $data);
    }

    public function product($slug)
    {
        //echo $slug;
        $product = Product::where('slug', $slug)->with('product_images')->first();
        if ($product == null) {
            abort(404);
        }
    
        //fetch related products
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productsArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productsArray)->with('product_images')->get();
        }
    
        return view('Front.product', compact('product', 'relatedProducts'));
    }

}