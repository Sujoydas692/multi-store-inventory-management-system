<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboardPage()
    {
        return view('pages.dashboard.dashboard-page');
    }



    public function summary(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $product = Product::where('user_id', $user_id)->count();
        $category = Category::where('user_id', $user_id)->count();
        $customer = Customer::where('user_id', $user_id)->count();
        $invoice = Invoice::where('user_id', $user_id)->count();


        $invoiceSummary = Invoice::where('user_id', $user_id)
        ->selectRaw('ROUND(SUM(total), 2) as total, ROUND(SUM(payable), 2) as payable')
        ->first();
            
        $todaySale = Invoice::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->sum('payable');

    
        $monthSale = Invoice::where('user_id', $user_id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('payable');

        return response()->json([
            'status' => 'success',
            'data' => [
                'product' => $product,
                'category' => $category,
                'customer' => $customer,
                'invoice' => $invoice,
                'total' => $invoiceSummary->total ?? 0,
                'payable' => $invoiceSummary->payable ?? 0,
                'today_sale' => round($todaySale, 2),
                'month_sale' => round($monthSale, 2),
            ]
        ], 200);
    }
}
