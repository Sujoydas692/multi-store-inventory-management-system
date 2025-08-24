<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function customerPage()
    {
        return view('pages.dashboard.customer-page');
    }



    // Create Customer
    public function createCustomer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => 'required|email',
                'mobile' => 'required|string|max:15'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation Fail',
                    'error' => $validator->errors()
                ], 422);
            }

            $data = $validator->validate();
            $user = Auth::user();

            Customer::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'request successful'
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Get all customer
    public function customerList(Request $request)
    {
        try {
            // $user = Auth::user();
            // $user_id = $user->id;
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'No user in Auth']);
            }

            $user_id = $user->id;

            $customer = Customer::where('user_id', $user_id)
                ->select(['id', 'name', 'email', 'mobile'])
                ->get();


            return response()->json([
                'status' => 'success',
                'message' => 'request successful',
                'data' => $customer
            ], 200);

        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // get Customer By ID
    public function customerById(Request $request)
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;
            $id = $request->input('id');

            $customer = Customer::where('user_id', $user_id)->where('id', $id)->first();

            return response()->json([
                'status' => 'success',
                'message' => 'request successful',
                'data' => $customer
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Customer Update
    public function customerUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => 'required|email',
                'mobile' => 'required|string|max:15'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation Fail',
                    'error' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $user_id = $user->id;
            $id = $request->input('id');

            $data = $validator->validate();

            $customer = Customer::Where('id', $id)->where('user_id', $user_id)->first();

            if (!$customer) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Customer not fund.'
                ], 404);
            }

            $customer->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'request successful',
                'data' => $customer->only(['id', 'name', 'email', 'mobile'])
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }


    // Customer Delete
    public function customerDelete(Request $request)
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;
            $id = $request->input('id');

            $customer = Customer::where('id', $id)->where('user_id', $user_id)->first();

            if (!$customer) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Customer not fund.'
                ], 404);
            }

            $customer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Customer delete successfully.'
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