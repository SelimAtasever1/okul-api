<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\PreorderService; 
use App\Services\SmsService; 

use App\Models\Preorder;

class PreorderController extends Controller
{
    protected $preorderService;
    protected $smsService;

    public function __construct(PreorderService $preorderService, SmsService $smsService)
    {
        $this->preorderService = $preorderService;
        $this->smsService = $smsService;
    }

    /**
     * @OA\Post(
     *     path="/preorders",
     *     summary="Create a new preorder",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com", nullable=true),
     *             @OA\Property(property="phone", type="string", example="5950555151"),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(response="200", description="User and product created successfully"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function createPreorder(Request $request)
    {
        try {
            $preorder = $this->preorderService->processPreorderRequest($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'User and product created successfully!',
                'preorder' => $preorder
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors(),
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/preorders",
     *     summary="Get all preorders",
     *     security={{"basicAuth": {}}},
     *     @OA\Response(response="200", description="List of all preorders"),
     *     @OA\Response(response="403", description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $preorders = Preorder::all();
        return response()->json($preorders);
    }

    /**
     * @OA\Get(
     *     path="/preorders/{id}",
     *     summary="Get a specific preorder by ID",
     *     security={{"basicAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the preorder to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Details of the specific preorder"),
     *     @OA\Response(response="404", description="Preorder not found"),
     *     @OA\Response(response="403", description="Unauthorized")
     * )
     */
    public function show($id)
    {
        $preorder = Preorder::find($id);
        if (!$preorder) {
            return response()->json(['message' => 'Preorder not found'], 404);
        }
        return response()->json($preorder);
    }

    /**
     * @OA\Put(
     *     path="/preorders/{id}/approve",
     *     summary="Approve a specific preorder by ID",
     *     security={{"basicAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the preorder to approve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Preorder approved successfully"),
     *     @OA\Response(response="404", description="Preorder not found"),
     *     @OA\Response(response="403", description="Unauthorized")
     * )
     */
    public function approve($id)
    {
        $preorder = Preorder::find($id);
        if (!$preorder) {
            return response()->json(['message' => 'Preorder not found'], 404);
        }

        $preorder->update(['status' => 'approved']);

        $this->smsService->send($preorder->phone, "Your preorder has been approved!");  // sms
        return response()->json(['message' => 'Preorder approved successfully']);
    }
}
