<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Services\FirebaseService;
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

            // Add a status filter if 'status' is provided in the request
            $status = $request->input('status');
            $paymentQuery = UserNotification::where('user_id', $user_id);

            if (! empty($status)) {
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

    public function testPushNotiification(Request $request)
    {
        try {
            //code...
            $token = 'drj0552ASH6RQrto7V0BkV:APA91bFAURpxNNNh_yiqcoNiq5BqeEeWJeQCZq2wCxoxE8LMOPBYYaSx7g_rGYg43wh7PtRlcyB3enmVECaBQ4t2vfYOb1pGtqxgbuXsAhWsmgZYYFMlgUdz96hM_Cd18lwQ9G9BsLWH';
            $title = 'Testing message';
            $message = 'Testing sending messages';

            $firebaseService = new FirebaseService();

            // return $firebaseService->getAccessToken()['access_token'];
            return $firebaseService->sendToDevice($token, $title, $message);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
