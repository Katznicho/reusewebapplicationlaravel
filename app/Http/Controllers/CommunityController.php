<?php

namespace App\Http\Controllers;

use App\Models\CommunityCategory;
use App\Models\CommunityDetails;
use App\Models\Delivery;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    use UserTrait;

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
                ],
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
                'userProducts',
            ])->first();

            return response()->json(['success' => true, 'data' => $community]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getCommunityDeliveries(Request $request)
    {
        try {
            //code...
            $limit = $request->input('limit', 100);
            $page = $request->input('page', 1);
            $sortOrder = $request->input('sort_order', 'desc');
            $user_id = $this->getCurrentLoggedUserBySanctum()->id;

            $status = $request->input('status');
            $paymentQuery = Delivery::where('community_id', $user_id);

            if (!empty($status)) {
                $paymentQuery->where('status', $status);
            }

            $res = $paymentQuery->orderBy('id', $sortOrder)->with([
                'user',
                'category',
                'product',
                'payment',
                'community',
            ])->paginate($limit, ['*'], 'page', $page);

            $response = [
                'data' => $res->items(),
                'pagination' => [
                    'current_page' => $res->currentPage(),
                    'per_page' => $limit,
                    'total' => $res->total(),
                ],
            ];

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    //get community totals
    public function getCommunityTotals(Request $request)
    {
        try {
            $user = $this->getCurrentLoggedUserBySanctum();
            $total_products = Product::where('community_id', $user->id)->count();
            $total_payments = Payment::where('user_id', $user->id)->sum('amount');
            $deliveries = Delivery::where('community_id', $user->id)->count();

            return response()->json(['success' => true, 'data' => [
                'total_products' => $total_products,
                'total_payments' => $total_payments,
                'total_deliveries' => $deliveries,
            ]]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getAllAvailableCommunities(Request $request)
    {
        try {
            //code...
            $communities = User::where('role', config('user_roles.user_roles.RECEIVER'))->get();

            return response()->json(['success' => true, 'data' => $communities]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function storeCommunityDetails(Request $request)
    {
        try {
            $request->validate([
                'purpose' => 'required',
                'location' => 'required',
                'community_category_id' => 'required',
                'user_id' => 'required',
                'contact_person' => 'required',
                'contact_number' => 'required',
                'contact_person_email' => 'required',
                'contact_person_role' => 'required',
                // 'website' => 'required',
                'total_members' => 'required',
                'total_members_women' => 'required',
                'total_members_men' => 'required',
                'year_started' => 'required',
                'leader_name' => 'required',
                'leader_role' => 'required',
                'leader_email' => 'required',
                'leader_contact' => 'required',
                'images' => 'required|array',
            ]);

            $user_id = $this->getCurrentLoggedUserBySanctum()->id;

            //update or create
            $community = CommunityDetails::updateOrCreate([
                'user_id' => $user_id,
            ], [
                'purpose' => $request->purpose,
                'location' => $request->location,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'community_category_id' => $request->community_category_id,
                'user_id' => $user_id,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'contact_person_email' => $request->contact_person_email,
                'contact_person_role' => $request->contact_person_role,
                'website' => $request->website,
                'total_members' => $request->total_members,
                'total_members_women' => $request->total_members_women,
                'total_members_men' => $request->total_members_men,
                'year_started' => $request->year_started,
                'leader_name' => $request->leader_name,
                'leader_role' => $request->leader_role,
                'leader_email' => $request->leader_email,
                'leader_contact' => $request->leader_contact,
                'images' => $request->images,
            ]);


            return response()->json(['success' => true, 'data' => $community]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getStoredCommunityDetails(Request $request)
    {

        try {
            $user = $this->getCurrentLoggedUserBySanctum();
            $community = CommunityDetails::where('user_id', $user->id)->first();
            if ($community) {
                return response()->json(['success' => true, 'data' => $community]);
            } else {
                return response()->json(['success' => true, 'data' => []]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getAllCommunityCatgeories(Request $request)
    {
        try {
            //code...
            $categories = CommunityCategory::all();

            return response()->json(['success' => true, 'data' => $categories]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
