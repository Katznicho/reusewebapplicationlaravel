<?php

namespace App\Http\Controllers;

use App\Mail\Payment;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    use UserTrait;

    //
    public function getDonorsByPage(Request $request)
    {
        try {
            $limit = $request->input('limit', 100);
            $page = $request->input('page', 1);
            $sortOrder = $request->input('sort_order', 'desc');
            $donors = User::orderBy('id', $sortOrder)->where('role', config('user_roles.DONOR'))->paginate($limit, ['*'], 'page', $page);
            $response = [
                'data' => $donors->items(),
                'pagination' => [
                    'current_page' => $donors->currentPage(),
                    'per_page' => $limit,
                    'total' => $donors->total(),
                ],
            ];

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    //get specific donor details with their products , deliveries and payments
    public function getDonorDetails(Request $request)
    {
        try {
            $donor = User::where('id', $request->donor_id)->with([
                'userPayments',
                'userDeliveries',
                'userProducts',
            ])->first();

            return response()->json(['success' => true, 'data' => $donor]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    //get donor totals ie products , deliveries , payments
    public function getDonorTotals(Request $request)
    {
        try {
            $user = $this->getCurrentLoggedUserBySanctum();
            $total_products = Product::where('user_id', $user->user_id)->count();
            $total_payments = Payment::where('user_id', $user->user_id)->sum('amount');
            $deliveries = Delivery::where('user_id', $user->user_id)->count();
            $total_deliveries = $user->userDeliveries->count();
            $total_payments = $user->userPayments->count();
            $response = [
                'total_products' => $total_products,
                'total_payments' => $total_payments,
                'total_deliveries' => $total_deliveries,
                'total_payments' => $total_payments,
                'total_deliveries' => $deliveries,

            ];

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
