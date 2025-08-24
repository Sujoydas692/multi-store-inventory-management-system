<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Web\PageController;
use App\Http\Middleware\JwtTokenVerify;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

// Backend
Route::group(['prefix'=>'backend'], function(){

    Route::post('register',[RegisterController::class,'register']);
    Route::post('login',[LoginController::class,'login']);
    Route::post('password/reset/send/otp',[ResetPasswordController::class,'sendOtp']);
    Route::post('password/reset/verify/otp',[ResetPasswordController::class,'verifyOtp']);
    Route::post('password/reset',[ResetPasswordController::class,'resetPassword']);

    Route::group(['middleware'=> JwtTokenVerify::class], function(){
        Route::get('profile',[ProfileController::class,'profile']);
        Route::post('profile-update',[ProfileController::class,'profileUpdate']);
        Route::post('logout',[LogoutController::class,'logout']);
    });

    // Category 
Route:: post('/create-category', [CategoryController::class, 'createCategory'])->middleware(JwtTokenVerify::class);
Route:: get('/all-category', [CategoryController::class, 'categoryList'])->middleware(JwtTokenVerify::class);
Route:: post('/category-by-id', [CategoryController::class, 'categoryById'])->middleware(JwtTokenVerify::class);
Route:: post('/update-category', [CategoryController::class, 'categoryUpdate'])->middleware(JwtTokenVerify::class);
Route:: post('/delete-category', [CategoryController::class, 'categoryDelete'])->middleware(JwtTokenVerify::class);


// Customer
Route:: post('/create-customer', [CustomerController::class, 'createCustomer'])->middleware(JwtTokenVerify::class);
Route:: get('/all-customer', [CustomerController::class, 'customerList'])->middleware(JwtTokenVerify::class);
Route:: post('/customer-by-id', [CustomerController::class, 'customerById'])->middleware(JwtTokenVerify::class);
Route:: post('/update-customer', [CustomerController::class, 'customerUpdate'])->middleware(JwtTokenVerify::class);
Route:: post('/delete-customer', [CustomerController::class, 'customerDelete'])->middleware(JwtTokenVerify::class);


// Products
Route:: post('/create-product', [ProductController::class, 'productCreate'])->middleware(JwtTokenVerify::class);
Route:: get('/all-product', [ProductController::class, 'productList'])->middleware(JwtTokenVerify::class);
Route:: post('/product-by-id', [ProductController::class, 'productById'])->middleware(JwtTokenVerify::class);
Route:: post('/update-product', [ProductController::class, 'productUpdate'])->middleware(JwtTokenVerify::class);
Route:: post('/delete-product', [ProductController::class, 'productDelete'])->middleware(JwtTokenVerify::class);

Route:: get('/product-with-stock', [ProductController::class, 'hasStock'])->middleware(JwtTokenVerify::class);


// Invoice
Route:: post('/create-invoice', [InvoiceController::class, 'invoiceCreate'])->middleware(JwtTokenVerify::class);
Route:: get('/all-invoice', [InvoiceController::class, 'invoiceSelect'])->middleware(JwtTokenVerify::class);
Route:: post('/detail-invoice', [InvoiceController::class, 'invoiceDetails'])->middleware(JwtTokenVerify::class);
Route:: post('/delete-invoice', [InvoiceController::class, 'invoiceDelete'])->middleware(JwtTokenVerify::class);


// Dashboard Summary
Route::get("/summary",[DashboardController::class,'summary'])->middleware([JwtTokenVerify::class]);


// Report Generation
Route::get("/sales-report/{FormDate}/{ToDate}",[ReportController::class,'salesReport'])->middleware([JwtTokenVerify::class]);
Route::get("/products-report",[ProductController::class,'productsReport'])->middleware([JwtTokenVerify::class]);

});


// Frontend Routes
Route::group(['middleware'=> RedirectIfAuthenticated::class], function () {
    Route::get('/', [PageController::class, 'index']);
    Route::get('/register', [PageController::class, 'registration'])->name('register');
    Route::get('/login', [PageController::class, 'login'])->name('login');
    Route::get('/reset-password', [PageController::class, 'resetPassword'])->name('reset-password');
    Route::get('/send-otp', [PageController::class, 'sendOtp'])->name('forgot-password.send-otp');
    Route::get('/verify-otp', [PageController::class, 'verifyOtp'])->name('forgot-password.verify-otp');
});


Route::group(['middleware'=> JwtTokenVerify::class], function(){
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [PageController::class, 'profile'])->name('profile');
    
});

Route:: get('/customerPage', [CustomerController::class, 'customerPage'])->name('customer')->middleware(JwtTokenVerify::class);
Route:: get('/categoryPage', [CategoryController::class, 'categoryPage'])->name('category')->middleware(JwtTokenVerify::class);
Route:: get('/productPage', [ProductController::class, 'productPage'])->name('product')->middleware(JwtTokenVerify::class);
Route:: get('/invoicePage', [InvoiceController::class, 'invoicePage'])->name('invoice')->middleware(JwtTokenVerify::class);
Route:: get('/salePage', [SaleController::class, 'salePage'])->name('salePage')->middleware(JwtTokenVerify::class);
Route:: get('/reportPage', [ReportController::class, 'reportPage'])->name('report')->middleware(JwtTokenVerify::class);

