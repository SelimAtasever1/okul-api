<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(title="Cart API", version="0.1")
 */
class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @OA\Post(
     *     path="/cart/store-logged-user-cart",
     *     summary="Stores cart items for a logged user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="quantity", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function storeAsLoggedUser(Request $request)
    {
        return $this->store($request, [$this->cartService, 'storeForLoggedUser']);
    }


    /**
     * @OA\Post(
     *     path="/cart/store-guest-cart",
     *     summary="Stores cart items for a guest user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe"),
     *             @OA\Property(property="phone", type="string", example="5050550550"),
     *             @OA\Property(property="email", type="string", format="email", example="email@example.com"),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function storeAsGuest(Request $request)
    {
        return $this->store($request, [$this->cartService, 'storeForGuest']);
    }

    protected function store(Request $request, callable $callback)
    {
        try {
            $response = $callback($request->all());
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    /**
     * @OA\Patch(
     *     path="/cart/update",
     *     summary="Updates cart items",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="id", type="integer", example=7),
     *                     @OA\Property(property="quantity", type="integer", example=30)
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=7),
     *                             @OA\Property(property="quantity", type="integer", example=30)
     *                         ),
     *                         example={{"id": 5, "quantity": 20}, {"id": 6, "quantity": 15}}
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response="200", description="Items updated successfully"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function update(Request $request)
    {
        $response = $this->cartService->updateCart($request->all());
        return response()->json($response);
    }

    /**
     * @OA\Delete(
     *     path="/cart/delete",
     *     summary="Deletes items from the cart",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="id", type="integer", example=5),
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="ids",
     *                         type="array",
     *                         @OA\Items(type="integer"),
     *                         example={5, 6, 7}
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response="200", description="Items removed from cart successfully"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function destroy(Request $request)
    {
        $response = $this->cartService->deleteFromCart($request->all());
        return response()->json($response);
    }
}
