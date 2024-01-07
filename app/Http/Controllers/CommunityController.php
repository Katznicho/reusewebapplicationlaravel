<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    //get all communitys by page
    public function getCommunitysByPage(Request $request)
    {
        try {
            $limit = $request->input('limit', 100);
            $page = $request->input('page', 1);
            $sortOrder = $request->input('sort_order', 'desc');
            $communitys = User::orderBy('id', $sortOrder)->where('role', config('user_roles.user_roles.RECEIVER'))->paginate($limit, ['*'], 'page', $page);
            $response = [
                'data' => $communitys->items(),
                'pagination' => [
                    'current_page' => $communitys->currentPage(),
                    'per_page' => $limit,
                    'total' => $communitys->total(),
                ]
            ];
            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    //get community details
    public function getCommunityDetails(Request $request)
    {
        try {
            $community = User::where('id', $request->community_id)->with([
                'userPayments',
                'userDeliveries',
                'userProducts'
            ])->first();
            return response()->json(['success' => true, 'data' => $community]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    //get community totals
    public function getCommunityTotals(Request $request)
    {
        try {
            $user =   $this->getCurrentLoggedUserBySanctum();
            $total_products = Product::where('community_id', $user->user_id)->count();
            $total_payments = Payment::where('community_id', $user->user_id)->sum('amount');
            $deliveries = Delivery::where('community_id', $user->user_id)->get();
            $total_deliveries = $user->userDeliveries->count();
            return response()->json(['success' => true, 'data' => [
                'total_products' => $total_products,
                'total_payments' => $total_payments,
                'total_deliveries' => $total_deliveries,
            ]]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
