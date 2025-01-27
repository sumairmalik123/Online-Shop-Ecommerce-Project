<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\ProductSubController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\ProductImageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
 //   return view('welcome');
//});
//Route::get('/test', function () {
   // orderEmail(20);
//});
Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/cart/add',[CartController::class,'addToCart'])->name('front.addtocart');
Route::post('/cart/update',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/cart/delete',[CartController::class,'removeCart'])->name('front.deletecart');
Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');
Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('front.processCheckout');
Route::get('/thanks/{orderId}',[CartController::class,'thankyou'])->name('front.thankyou');
Route::post('/get-order-summary', [CartController::class, 'getOrdersummary'])->name('front.getOrdersummary');
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('front.applyDiscount');
Route::post('/remove-discount', [CartController::class, 'removeCoupon'])->name('front.removeCoupon');
Route::post('/add-to-wishlist', [FrontController::class, 'addToWishlist'])->name('front.addToWishlist');
Route::get('/page/{slug}',[FrontController::class,'showfrontpage'])->name('front.showfrontpage');
Route::post('/send-contact-email',[FrontController::class,'sendContactEmail'])->name('front.sendContactEmail');
Route::get('/forgot-password',[AuthController::class,'forgotPasword'])->name('front.forgotPasword');
Route::post('/process-forgot-password',[AuthController::class,'sendForgotPasswordLink'])->name('front.sendForgotPasswordLink');
Route::get('/reset-password/{token}',[AuthController::class,'resetPassword'])->name('front.resetpassword');
Route::post('/process-reset-password',[AuthController::class,'processResetPassword'])->name('front.processResetPassword');
Route::post('/save-rating/{productId}',[ShopController::class,'saveRating'])->name('front.saveRating');







// routes/web.php




Route::group(['prefix' => 'user'], function(){
    Route::group(['middleware' => 'guest'], function(){
        Route::get('/login',[AuthController::class,'login'])->name('user.login');
        Route::get('/register',[AuthController::class,'register'])->name('user.register');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('user.processRegister');
        Route::post('/authenticate',[AuthController::class,'authenticate'])->name('user.authenticate');
    });

    Route::group(['middleware' => 'auth'], function(){
        Route::get('/dashboard',[AuthController::class,'dashboard'])->name('user.dashboard');
        Route::post('/update-profile',[AuthController::class,'updateProfile'])->name('user.updateProfile');
        Route::post('/update-address',[AuthController::class,'updateAddress'])->name('user.updateAddress');
        Route::get('/change-password',[AuthController::class,'showChangePasswordForm'])->name('user.changePassword');
        Route::post('/process-change-password',[AuthController::class,'changePassword'])->name('user.changePasswordproecess');
        Route::get('/my-orders',[AuthController::class,'orders'])->name('user.orders');
        Route::get('/my-wishlist',[AuthController::class,'wishlist'])->name('user.wishlist');
        Route::post('/remove-product-from-wishlist',[AuthController::class,'removeProductFromWishlist'])->name('user.removeProductFromWishlist');
        Route::get('/order-detail/{orderId}',[AuthController::class,'orderDetail'])->name('user.orderDetail');
        Route::get('/logout',[AuthController::class,'logout'])->name('user.logout');
        
    });
});



//auth middleware
Route::prefix('account')->group(function () {
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('logout', [LoginController::class, 'logout'])->name('account.logout');
        Route::get('dashboard',[DashboardController::class,'index'])->name('account.dashboard');

        //category route
        Route::get('/categories/list',[CategoryController::class,'index'])->name('categories.list');
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit',[CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        //image dropzone
        Route::post('/uplode-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');

        //subcategory route
        Route::get('/subcategories/list',[SubCategoryController::class,'index'])->name('subcategories.list');
        Route::get('/subcategories/create',[SubCategoryController::class,'create'])->name('subcategories.create');
        Route::post('/subcategories/store', [SubCategoryController::class, 'store'])->name('subcategories.store');
        Route::get('/subcategories/{subcategory}/edit',[SubCategoryController::class, 'edit'])->name('subcategories.edit');
        Route::post('/subcategories/{subcategory}', [SubCategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('/subcategories/{subcategory}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');

        //brands route
        Route::get('/brands/list',[BrandController::class,'index'])->name('brands.list');
        Route::get('/brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('/brands/store', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit',[BrandController::class, 'edit'])->name('brands.edit');
        Route::post('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

        //product route
        Route::get('/product/list',[ProductController::class,'index'])->name('products.list');
        Route::get('/product/create',[ProductController::class,'create'])->name('products.create');
        Route::post('/product/store', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/edit/{product}',[ProductController::class, 'edit'])->name('products.edit');
        Route::post('/product/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        // realted product fetch
        Route::get('/get-products', [ProductController::class, 'getRelatedProduct'])->name('products.getproducts');
        // rating route
        Route::get('/ratings',[ProductController::class,'productRatings'])->name('products.productRatings');
        Route::get('/change-rating-status',[ProductController::class,'changeRatingStatus'])->name('products.changeRatingStatus');





        //productsubcategory route
        Route::get('/product-subcatogries',[ProductSubController::class,'index'])->name('product-subcatogries.index');
        //productImage route udate
        Route::post('/product-images/update',[ProductImageController::class,'update'])->name('product-images.update');
        Route::delete('/product-images/destroy',[ProductImageController::class,'destroy'])->name('product-images.destroy');

        //shipping routes
        Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping.create');
        Route::post('/shipping',[ShippingController::class,'store'])->name('shipping.store');
        Route::get('/shipping/{id}',[ShippingController::class,'edit'])->name('shipping.edit');
        Route::put('/shipping/{id}',[ShippingController::class,'update'])->name('shipping.update');
        Route::delete('/shipping/{id}',[ShippingController::class,'destroy'])->name('shipping.destroy');


        //coupon routes
        Route::get('/coupon/list',[DiscountCodeController::class,'index'])->name('coupon.list');
        Route::get('/coupon/create',[DiscountCodeController::class,'create'])->name('coupon.create');
        Route::post('/coupon/store', [DiscountCodeController::class, 'store'])->name('coupon.store');
        Route::get('/coupon/edit/{coupon}',[DiscountCodeController::class, 'edit'])->name('coupon.edit');
        Route::post('/coupon/{coupon}', [DiscountCodeController::class, 'update'])->name('coupon.update');
        Route::delete('/coupon/{coupon}', [DiscountCodeController::class, 'destroy'])->name('coupon.destroy');

        //order routes
        Route::get('/order',[OrderController::class,'index'])->name('order.list');
        Route::get('/order/{id}',[OrderController::class,'detail'])->name('order.detail');
        Route::post('/order/change-status/{id}',[OrderController::class,'changeOrderStatus'])->name('order.changeOrderStatus');
        Route::post('/order/send-invoice/{id}',[OrderController::class,'sendInvoiceEmail'])->name('order.sendInvoiceEmail');

        //users route
        Route::get('/users',[UserController::class,'index'])->name('users.list');
        Route::get('/users/create',[UserController::class,'create'])->name('users.create');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{usersId}/edit',[UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/{usersId}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{usersId}', [UserController::class, 'destroy'])->name('users.destroy');

        //pages route
        Route::get('/pages',[PageController::class,'index'])->name('pages.list');
        Route::get('/pages/create',[PageController::class,'create'])->name('pages.create');
        Route::post('/pages/store', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{pagesId}/edit',[PageController::class, 'edit'])->name('pages.edit');
        Route::post('/pages/{pagesId}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{pagesId}', [PageController::class, 'destroy'])->name('pages.destroy');

        //get slug method
        Route::get('/getSlug', function(Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');

    });


    //without auth middleware
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('login',[LoginController::class,'index'])->name('account.login');
        Route::get('register', [LoginController::class, 'register'])->name('account.register');
        Route::post('process-register', [LoginController::class, 'processregister'])->name('account.processregister');
        Route::post('/authenticate',[LoginController::class,'authenticate'])->name('account.authenticate');
        
    });

    //setting route
    Route::get('/admin-change-password', [SettingController::class, 'showChangeAdminPasswordForm'])->name('account.showChangeAdminPasswordForm');
    Route::post('/admin-process-password', [SettingController::class, 'processChangePassword'])->name('account.processChangePassword');







});


