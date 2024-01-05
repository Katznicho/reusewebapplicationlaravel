<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Traits\UserTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use UserTrait;
    //
    public function getUserNotifications(Request $request)
    {
        try {
            $limit = $request->input('limit', 100);
            $page = $request->input('page', 1);
            $sortOrder = $request->input('sort_order', 'desc');
            $user_id = $this->getCurrentLoggedUserBySanctum()->id;
            //code...
            // Add a status filter if 'status' is provided in the request
            $status = $request->input('status');
            $paymentQuery = UserNotification::where('user_id', $user_id);

            if (!empty($status)) {
                $paymentQuery->where('status', $status);
            }

            $res = $paymentQuery->orderBy('id', $sortOrder)->with([
                'user',
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
}
