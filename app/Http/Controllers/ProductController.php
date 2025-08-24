<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function productPage()
    {
        return view('pages.dashboard.product-page');
    }




    // Create Product
    public function productCreate(Request $request)
    {
        try {
            //  prepare Image name
            $user = Auth::id();
            $img = $request->file('img');
            $time = time();
            $file_path = $img->getClientOriginalName();

            $image_name = "{$user}-{$time}-{$file_path}";

            $image_url = $img->storeAs('products', $image_name, 'public');

            // Save to database
            $product = Product::create([
                'name' => $request->input('name'),
                'buy_price' => $request->input('buy_price'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'stock_qty' => $request->input('stock_qty'),
                'category_id' => $request->input('category_id'),
                'img_url' => $image_url,
                'user_id' => $user
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // get all product
    public function productList(Request $request)
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;

            $product = Product::where('user_id', $user_id)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'request successful',
                'data' => $product
            ], 200);



        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    public function productsReport()
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;

            $products = Product::where('user_id', $user_id)->get();

            $data = [
                'products' => $products
            ];

            $pdf = Pdf::loadView('report.products_report', $data);
            return $pdf->download('products_report.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // get product by id
    public function productById(Request $request)
    {
        try {
            $product_id = $request->input('id');
            $user = Auth::user();
            $user_id = $user->id;

            $product = Product::where('id', $product_id)->where('user_id', $user_id)->first();

            return response()->json([
                'status' => 'success',
                'message' => 'request successful',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Update Product
    public function productUpdate(Request $request)
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;

            $product_id = $request->input('id');

            $product = Product::where('id', $product_id)
                ->where('user_id', $user_id)
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Product not found or access denied.'
                ], 404);
            }

            $updateData = [
                'name' => $request->input('name'),
                'buy_price' => $request->input('buy_price'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'stock_qty' => $request->input('stock_qty'),
                'category_id' => $request->input('category_id'),
            ];

            // Save image to folder.
            if ($request->hasFile('img')) {
                $img = $request->file('img');
                $time = time();
                $file_path = $img->getClientOriginalName();

                $image_name = "{$user_id}-{$time}-{$file_path}";

                $image_url = $img->storeAs('products', $image_name, 'public');

                // Delete old image
                if (!empty($product->img_url)) {
                    Storage::disk('public')->delete($product->img_url);
                }

                $updateData['img_url'] = $image_url;
            }

            // Update Product
            Product::where('id', $product_id)
                ->where('user_id', $user_id)
                ->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Product update successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Delete Product
    public function productDelete(Request $request)
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;
            $product_id = $request->input('id');

            $product = Product::where('id', $product_id)->where('user_id', $user_id)->first();

            if (!$product) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Product not found'
                ], 404);
            }

            // delete product image from folder
            if (!empty($product->img_url)) {
                Storage::disk('public')->delete($product->img_url);
            }

            // delete product from database 
            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'you Sold this product so delete is not possible',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Show product if has stock
    public function hasStock(Request $request)
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;

            $product = Product::where('user_id', $user_id)
                ->where('stock_qty', '>', 1)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'request successful',
                'data' => $product
            ], 200);



        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


}
