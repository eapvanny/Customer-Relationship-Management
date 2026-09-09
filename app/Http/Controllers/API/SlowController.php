<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class SlowController extends Controller
{
    public function index()
    {
        $customer = Customer::pluck('phone')->toArray();
        return response()->json([
            'status' => true,
            'message' => 'Customer Phone Numbers',
            'data' => $customer
        ]);
    }
}
