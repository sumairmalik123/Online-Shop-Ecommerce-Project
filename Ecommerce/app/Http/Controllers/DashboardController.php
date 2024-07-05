<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    //dashboard page show
    public function index(){
     $totalOrders = Order::where('status','!=','cancelled')->count();
     $totalProducts = Product::count();
     $totalUsers = Auth::user()->where('role',1)->count();
     $totalRevenue = Order::where('status','!=','cancelled')->sum('grand_total');

     //THIS MONTH REVENUE
     $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
    // $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
    $currentDate = Carbon::now()->format('Y-m-d');
    $revenueThisMonth = Order::where('status','!=','cancelled')
    ->whereDate('created_at','>=',$startOfMonth)
    ->whereDate('created_at','<=',$currentDate)
    ->sum('grand_total');

    //last Month
    $lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
    $endOfMonth = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
    $lastMonthName = Carbon::now()->subMonth()->startOfMonth()->format('M');
    $revenueLastMonth = Order::where('status','!=','cancelled')
    ->whereDate('created_at','>=',$lastMonthStartDate)
    ->whereDate('created_at','<=',$endOfMonth)
    ->sum('grand_total');

    //Last 30 days sale
    $last30Days = Carbon::now()->subDays(30)->format('Y-m-d');
    $revenueLast30Days = Order::where('status','!=','cancelled')
    ->whereDate('created_at','>=',$last30Days)
    ->whereDate('created_at','<=',$currentDate)
    ->sum('grand_total');

    //Delete Temp Image
     $dayBeforeToday = Carbon::now()->subDays(1)->format('Y-m-d H:i:s');
    $temImage = TempImage::where('created_at','<=',$dayBeforeToday)->get();
    foreach($temImage as $image){
        $tempPath = public_path('temp/' . $image->name);
        $thumbPath = public_path('/temp/thumb/' . $image->name);
       if(File::exists($tempPath)){
           File::delete($tempPath);
       } else {
        Log::info("Temp image file not found: $tempPath");
    }
       if(File::exists($thumbPath)){
           File::delete($thumbPath);
       }{
        Log::info("Temp thumb image file not found: $thumbPath");
    }

       TempImage::where('id',$image->id)->delete();
    }

        $user = Auth::user();
        return view('dashboard',[
            'user' => $user,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'revenueThisMonth' => $revenueThisMonth,
            'revenueLastMonth' => $revenueLastMonth,
            'revenueLast30Days' => $revenueLast30Days,
            'lastMonthName' => $lastMonthName
        ]);
    }
}
