<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function categoryPage(){
        return view ('pages.dashboard.category-page');
    }



    // Create Category
    public function createCategory(Request $request){
              
       try{
        $validator= Validator::make($request->all(), [
            'name'=> 'required|string|max:50'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=> 'fail',
                'message'=> 'validation Error',
                'errors'=> $validator->errors()
            ],422);
        }

        $user = Auth::user();
        $user_id = $user->id;

        Category::create([
            'name'=> $request->input('name'),
            'user_id'=> $user_id
        ]);

        return response()->json([
            'status'=> 'success',
            'message'=> 'Category Created'
        ], 200);

       }catch(\Exception $e){
        Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong',
            'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
        ], 500);
       }
    }


    // Get all category
    public function categoryList (Request $request){
        try{
            $user = Auth::user();
            $user_id = $user->id;

            $Category= Category::where('user_id', $user_id)
            ->select('id', 'name', 'user_id')
            ->get();

            return response()->json([
                'status'=> 'success',
                'message'=> 'All Category',
                'data'=> $Category
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }



    // Get Category By Id
    public function categoryById(Request $request){
        try{
            $categoryId= $request->input('id');
            $user = Auth::user();
            $user_id = $user->id;

            $Category= Category::where('id', $categoryId)->where('user_id', $user_id)->first();

            return $Category->only(['id', 'name']);


        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Update Category
    public function categoryUpdate(Request $request){
        try{
            $validator= Validator::make($request->all(), [
                'name'=> 'required|string|max:50'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'status'=> 'fail',
                    'message'=> 'validation Error',
                    'errors'=> $validator->errors()
                ],422);
            }

            $data= $validator->validate();
            $categoryId= $request->input('id');
            $user = Auth::user();
            $user_id = $user->id;
    
            Category::where('id', $categoryId)->where('user_id', $user_id)->update([
                'name'=> $data['name'],
                'user_id'=> $user_id
            ]);

            return response()->json([
                'status'=> 'success',
                'message'=> 'Category Updated'
            ], 200);
    
    
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Delete Category
    public function categoryDelete(Request $request){
        try{
            $categoryId= $request->input('id');
            $user = Auth::user();
            $user_id = $user->id;
    
            Category::where('id', $categoryId)->where('user_id', $user_id)->delete();

            return response()->json([
                'status'=> 'success',
                'message'=> 'Category delete successfully'
            ],200);    
    
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

}
