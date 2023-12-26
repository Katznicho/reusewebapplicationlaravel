<?php

namespace App\Http\Controllers;

use App\Mail\Payment as MailPayment;
use App\Models\Donation;
use App\Models\Payment;
use App\Models\UserAccount;
use App\Payments\Pesapal;
use App\Traits\UserTrait;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PaymentController extends Controller
{
    use UserTrait;


    private function  finishPaymentAndSendEmailByView(Payment $transaction, User $customer)
    {
        if ($transaction->type == config('status.payment_type.Donation')) {
            //createOrUpdate Donation
            Donation::updatedOrCreate(
                ['payment_id' => $transaction->id],
                [
                    'name' => "Reuse Donation",
                    'description' => $transaction->description,
                    'user_id' => $customer->id,
                    'is_annyomous' => $transaction->is_annyomous,
                    'status' => config('status.payment_status.completed'),
                    'payment_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'product_id' => $transaction->product_id,
                ]
            );
            try {
                Mail::to($customer->email)->send(new MailPayment($customer, 'Your Donation Has Been Successfully Completed', 'Donation Completed'));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }
            return view('payments.finish');
        } elseif ($transaction->type == config('status.payment_type.Wallet')) {

            //update the current user account balance get the account balance from the user account and add the amount
            $account = UserAccount::where('user_id', $customer->id)->first();
            UserAccount::where('user_id', $customer->id)->update([
                'account_balance' => $account->account_balance + $transaction->amount,
            ]);

            try {
                Mail::to($customer->email)->send(new MailPayment($customer, 'Your Wallet Balance Has Been Successfully Updated', 'Wallet TopUp Completed'));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }
            return view('payments.finish');
        } elseif ($transaction->type == config('status.payment_type.Product')) {
            try {
                Mail::to($customer->email)->send(new MailPayment($customer, 'THe  product payment has been successfully completed', 'Product Payment Completed'));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }
            return view('payments.finish');
        } else {
            return view('payments.finish');
        }
    }
    //
    private function  finishPaymentAndSendEmailByJSON(Payment $transaction, User $customer)
    {
        if ($transaction->type == config('status.payment_type.Donation')) {
            //createOrUpdate Donation
            Donation::updatedOrCreate(
                ['payment_id' => $transaction->id],
                [
                    'name' => "Reuse Donation",
                    'description' => $transaction->description,
                    'user_id' => $customer->id,
                    'is_annyomous' => $transaction->is_annyomous,
                    'status' => config('status.payment_status.completed'),
                    'payment_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'product_id' => $transaction->product_id,
                ]
            );
            try {
                Mail::to($customer->email)->send(new MailPayment($customer, 'Your Donation Has Been Successfully Completed', 'Donation Completed'));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }
            return view('payments.finish');
        } elseif ($transaction->type == config('status.payment_type.Wallet')) {

            //update the current user account balance get the account balance from the user account and add the amount
            $account = UserAccount::where('user_id', $customer->id)->first();
            UserAccount::where('user_id', $customer->id)->update([
                'account_balance' => $account->account_balance + $transaction->amount,
            ]);

            try {
                Mail::to($customer->email)->send(new MailPayment($customer, 'Your Wallet Balance Has Been Successfully Updated', 'Wallet TopUp Completed'));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Transaction completed',
            ]);
        } elseif ($transaction->type == config('status.payment_type.Product')) {
            try {
                Mail::to($customer->email)->send(new MailPayment($customer, 'THe  product payment has been successfully completed', 'Product Payment Completed'));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Transaction completed',
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Transaction completed',
            ]);
        }
    }
    public function finishPayment(Request $request)
    {
        try {
            //code...
            $orderTrackingId = $request->input('OrderTrackingId');
            $reference = $request->input('OrderMerchantReference');

            Payment::where('reference', $reference)->update([
                'order_tracking_id' => $orderTrackingId,

            ]);
            //get the actual transaction
            $transaction = Payment::where('reference', $reference)->first();
            if (!$transaction) {
                Log::error('Transaction does not exist');

                return view('payments.cancel');
            }
            $customer = User::find($transaction->user_id);
            $data = Pesapal::transactionStatus($orderTrackingId, $orderTrackingId);
            $payment_method = $data->message->payment_method;

            if ($data->message->payment_status_description == config('status.payment_status.completed')) {
                $message = "Hello {$customer->name} your payment of {$transaction->amount} has been successfully completed.Thank you";

                //check if the transaction is already completed
                if ($transaction->status == config('status.payment_status.completed')) {

                    return $this->finishPaymentAndSendEmailByView($transaction, $customer);
                } else {
                    $transaction->update([
                        'status' => config('status.payment_status.completed'),
                        'payment_method' => $payment_method,
                    ]);
                    return $this->finishPaymentAndSendEmailByView($transaction, $customer);
                }

                // $this->sendMessage($)

            } else {
                $transaction->update([
                    'status' => config('status.payment_status.failed'),
                ]);
                try {
                    Mail::to($customer->email)->send(new MailPayment($customer, 'Your Payment Failed', 'Payment Failed'));
                } catch (Throwable $th) {
                    // throw $th;
                    Log::error($th);
                }

                return view('payments.cancel');
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());

            return view('payments.finish');
        }
    }

    public function registerIPN(Request $request)
    {
        try {
            //add validation for url is registered
            $request->validate([
                'url' => 'required|string',
            ]);

            return Pesapal::pesapalRegisterIPN($request->url);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function listIPNS(Request $request)
    {
        try {
            $data = Pesapal::listIPNS();

            return response()->json(['success' => true, 'message' => 'Success', 'response' => $data]);
        } catch (\Throwable $th) {

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function cancelPayment(Request $request)
    {
        try {
            $payment_reference = $request->input('payment_reference');
            Payment::where('reference', $payment_reference)->update([
                'status' => config('status.payment_status.canceled'),
            ]);

            return view('payments.cancel');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return view('payments.cancel');
        }
    }

    public function completePayment(Request $request)
    {
        try {
            Log::info('===========The call back was called===================================');
            Log::info('Received Response Page');
            Log::info('============The call back was called==================================');
            // Get the parameters from the URL
            $orderTrackingId = $request->input('OrderTrackingId');
            $orderMerchantReference = $request->input('OrderMerchantReference');
            $orderNotificationType = $request->input('OrderNotificationType');
            Payment::where('reference', $orderMerchantReference)->update([
                'order_tracking_id' => $orderTrackingId,
                'orderNotificationType' => $orderNotificationType,

            ]);

            $transaction = Payment::where('reference', $orderMerchantReference)->first();
            if (!$transaction) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Transaction not found',
                ]);
            }
            $customer = User::find($transaction->customer_id);
            $data = Pesapal::transactionStatus($orderTrackingId, $orderTrackingId);
            $payment_method = $data->message->payment_method;

            Log::info('=========================================call back executed=============================================================================================================');
            Log::info("Received Response Page - Order Tracking ID: $orderTrackingId, Merchant Reference: $orderMerchantReference, Notification Type: $orderNotificationType");
            Log::info('==========================================call back executed============================================================================================================');

            if ($data->message->payment_status_description == config('status.payment_status.completed')) {


                //check if the transaction is already completed
                if ($transaction->status == config('status.payment_status.completed')) {
                    return $this->finishPaymentAndSendEmailByJson($transaction, $customer);
                } else {

                    $transaction->update([
                        'status' => 'completed',
                        'payment_method' => $payment_method,
                    ]);
                    return $this->finishPaymentAndSendEmailByJson($transaction, $customer);
                }
            }
        } catch (\Throwable $th) {

            Log::info('===========callback url==================================');
            Log::error($th->getMessage());
            Log::info('============call back url=================================');

            return response()->json(['success' => false, 'message' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function processOrder(Request $request)
    {
        try {
            //$amount, $phone, $callback
            $request->validate([
                'amount' => 'required|numeric',
                'phone_number' => 'required|string',
                'callback' => 'required|string',
                'payment_type' => 'required|string',
                'cancel_url' => 'required|string',
            ]);
            $getCustomer = $this->getCurrentLoggedUserBySanctum();

            if (!$getCustomer) {
                return response()->json(['success' => false, 'message' => 'Customer not found']);
            }
            $amount = $request->input('amount');
            $phone = $request->input('phone_number');
            $callback = $request->input('callback');
            $reference = Str::uuid();
            $description = $request->input('description') ?? 'Depositing on my wallet';
            $names = $getCustomer->name;
            $email = $getCustomer->email;
            $customer_id = $getCustomer->id;
            $cancel_url = $request->input('cancel_url');
            //add the payment reference to cancel url
            $cancel_url = $cancel_url . '?payment_reference=' . $reference;
            $payment_type = $request->input('payment_type');
            // return $payment_type;
            // return $amount;
            $data = Pesapal::orderProcess($reference, $amount, $phone, $description, $callback, $names, $email, $customer_id, $cancel_url, $payment_type);

            return response()->json(['success' => true, 'message' => 'Order processed successfully', 'response' => $data]);
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function checkTransactionStatus(Request $request)
    {

        try {
            //code...
            $request->validate([
                'orderTrackingId' => 'required|string',
                'merchantReference' => 'required|string',
            ]);
            $orderTrackingId = $request->input('orderTrackingId');
            $merchantReference = $request->input('merchantReference');
            $data = Pesapal::transactionStatus($orderTrackingId, $merchantReference);

            return response()->json(['success' => true, 'message' => 'Success', 'response' => $data->message->payment_status_description]);
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function testSendingMessages(Request $request)
    {
        try {
            //code...
            $message = 'Testing sending messages';
            $phoneNumber = '+256759983853';
            $res = $this->sendMessage($phoneNumber, $message);

            return response()->json(['success' => true, 'message' => 'Success', 'response' => $res]);

            return 'success';
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
