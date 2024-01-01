<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUserProducts(Request $request)
    {
        try {
            //code...
            $limit = $request->input('limit', 100);
            $page = $request->input('page', 1);
            $sortOrder = $request->input('sort_order', 'desc');
            $user_id = $this->getCurrentLoggedUserBySanctum()->id;

            // Add a status filter if 'status' is provided in the request
            $status = $request->input('status');
            $paymentQuery = Product::where('user_id', $user_id);

            if (!empty($status)) {
                $paymentQuery->where('status', $status);
            }

            $res = $paymentQuery->orderBy('id', $sortOrder)->with([
                'user',
                'delivery',
                'category'
            ])->paginate($limit, ['*'], 'page', $page);

            $response = [
                "products" => $res->items(),
                "pagination" => [
                    'current_page' => $res->currentPage(),
                    'per_page' => $limit,
                    'total' => $res->total(),
                ]
            ];

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getUserDelivries(Request $request)
    {
        try {
            //code...
            $limit = $request->input('limit', 100);
            $page = $request->input('page', 1);
            $sortOrder = $request->input('sort_order', 'desc');
            $user_id = $this->getCurrentLoggedUserBySanctum()->id;

            // Add a status filter if 'status' is provided in the request
            $status = $request->input('status');
            $paymentQuery = Delivery::where('user_id', $user_id);

            if (!empty($status)) {
                $paymentQuery->where('status', $status);
            }

            $res = $paymentQuery->orderBy('id', $sortOrder)->with([
                'user',
                'category',
                'product',
            ])->paginate($limit, ['*'], 'page', $page);

            $response = [
                "deliveries" => $res->items(),
                "pagination" => [
                    'current_page' => $res->currentPage(),
                    'per_page' => $limit,
                    'total' => $res->total(),
                ]
            ];

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function createProduct(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'price' => 'required',
                'cover_image' => 'required',
                'images' => 'required',
                'pick_up_location' => 'required',
                'weight' => 'required',
                'is_delivery_available' => 'required|boolean',
                'is_donation' => 'required|boolean',
                'is_product_new' => 'required|boolean',
                'is_product_available_for_all' => 'required|boolean',
                'is_product_damaged' => 'required|boolean',
                'is_product_rejected' => 'required|boolean',
                'is_product_accepted' => 'required|boolean',
                'status' => 'required',
                'total_amount' => 'required',
                'user_id' => 'required',
                'category_id' => 'required',


            ]);
            $res = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'cover_image' => $request->cover_image,
                'images' => $request->images,
                'pick_up_location' => $request->pick_up_location,
                'weight' => $request->weight,
                'is_delivery_available' => $request->is_delivery_available,
                'is_donation' => $request->is_donation,
                'is_product_new' => $request->is_product_new,
                'is_product_available_for_all' => $request->is_product_available_for_all,
                'is_product_damaged' => $request->is_product_damaged,
                'is_product_rejected' => $request->is_product_rejected,
            ]);
            if ($res) {
                return response()->json(['success' => true, 'message' => 'Product created successfully', 'data' => $res]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to create product']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
