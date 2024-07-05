<?php
use App\Models\Page;
use App\Models\Order;
use App\Models\Country;
use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

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
function orderEmail($orderId, $userType="customer")
{
    $order = Order::where('id', $orderId)->with('items')->first();


    if (!$order) {
        // Handle order not found scenario (optional)
        Log::error("Order with ID $orderId not found for sending email.");
        return;
    }
    if ($userType == 'customer') {
    
            $subject = 'Thanks for your Order!'; // Use more descriptive subject
            $email = $order->email; // Use more descriptive view name

    } else {
        
            $subject = 'Ypu have received an order'; // Use more descriptive subject
            $email = env('ADMIN_EMAIL');
        
    }

    $mailData = [
        'subject' => $subject, // Use more descriptive subject
        'order' => $order,
        'userType' => $userType
    ];

    Mail::to($order->email)->send(new OrderEmail($mailData));
}

function staticPages() {
    $pages = Page::orderBy('name', 'ASC')->get();
    return $pages;
}

function getCountryInfo($id) {
    $country = Country::where('id', $id)->first();
    return $country;
}

?>