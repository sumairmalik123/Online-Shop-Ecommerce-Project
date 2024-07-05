<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Validator;

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
          //front search method
        if (!empty($request->get('search') != '')) {
            $products = $products->where('title', 'like', '%' . $request->get('search') . '%');
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
        $product = Product::where('slug', $slug)
        ->withCount('product_ratings')
        ->withSum('product_ratings','rating')
        ->with(['product_images','product_ratings'])
        ->first();
        if ($product == null) {
            abort(404);
        }
    
        //fetch related products
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productsArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productsArray)->where('status',1)->with('product_images')->get();
        }

        //Rating Calculation
        //"product_ratings_count" => 2
        //"product_ratings_sum_rating" => 9.0
        $avgRating = '0.00';
        $avgRatingPer = 0;
        if ($product->product_ratings_count > 0) {
        $avgRating = number_format(($product->product_ratings_sum_rating/$product->product_ratings_count),2);
        $avgRatingPer = ($avgRating*100)/5;
        }
    
        return view('Front.product', compact('product', 'relatedProducts','avgRating','avgRatingPer'));
    }
    public function saveRating(Request $request, $productId){
    $validator = Validator::make($request->all(),[
            'name' => 'required|min:6',
            'email' => 'required|email',
            'comment' => 'required|min:10',
            'rating' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }


        $count = ProductRating::where('email', $request->email)->count();
        if ($count > 0) {
        session()->flash('error', 'You have already rated this product.');
        return response()->json([
            'status' => true, // Indicate error
            'message' => 'You have already rated this product.',
        ]);
    }
        $productRating = new ProductRating;
        $productRating->product_id = $productId;
        $productRating->username = $request->name;
        $productRating->email = $request->email;
        $productRating->comment = $request->comment;
        $productRating->rating = $request->rating;
        $productRating->status = 0;
        $productRating->save();
        session()->flash('success','Thank you for your feedback!');
        return response()->json([
            'status' => true,
            'message' => 'Thank you for your feedback!'
        ]);
    }

}