<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class SlowController extends Controller
{
    public function index(Request $request)
    {
        $since = $request->input('since');

        $query = Customer::query();

        // Only filter when "since" has a value
        if (!empty($since)) {
            $query->where('updated_at', '>', $since);
        }

        $customer = $query->pluck('phone')->toArray();

        return response()->json([
            'status' => true,
            'message' => 'Customer Phone Numbers',
            'data' => $customer,
        ]);
    }
}
