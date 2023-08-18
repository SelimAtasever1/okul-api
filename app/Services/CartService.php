<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cart;
use App\Models\Preorder;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CartService
{
    protected $preorderService;

    public function __construct(PreorderService $preorderService)
    {
        $this->preorderService = $preorderService;
    }

    public function storeForLoggedUser($data)
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::find($data['user_id']);

        $items = isset($data['items']) ? $data['items'] : [$data];

        $itemValidationRules = [
            'items.*.product_id' => 'required|integer|exists:product_pool,id',
            'items.*.quantity' => 'required|integer|min:1'
        ];

        foreach ($items as $item) {
            $validator = Validator::make($item, $itemValidationRules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }

        $cartItems = [];
        
        foreach ($items as $item) {
            $preorderData = [
                'name' => $user->name,          
                'lastname' => $user->lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            ];

            $preorder = $this->preorderService->processPreorderRequest($preorderData);

            $cartItems[] = Cart::create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'user_id' => $data['user_id'] 
            ]);
        }

        return [
            'status' => 'success',
            'message' => 'Preorders created and items added to cart successfully!',
            'cart_items' => $cartItems 
        ];
    }


    public function storeForGuest($data)
    {
        $items = isset($data['items']) ? $data['items'] : [$data];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $itemValidationRules = [
            'product_id' => 'required|integer|exists:product_pool,id',
            'quantity' => 'required|integer|min:1'
        ];

        $preorders = [];
        $cartItems = [];

        foreach ($items as $item) {
            $validator = Validator::make($item, $itemValidationRules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $preorderData = [
                'name' => $data['name'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            ];

            $preorders[] = $this->preorderService->processPreorderRequest($preorderData);

            $cartItems[] = Cart::create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            ]);
        }

        return [
            'status' => 'success',
            'message' => 'Preorders created and items added to cart successfully!',
            'preorders' => $preorders,
            'cart_items' => $cartItems
        ];
    }

    public function updateCart($data)
    {
        $validator = Validator::make($data, [
            'id' => 'sometimes|required|integer|exists:shopping_cart,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'items.*.id' => 'sometimes|required|integer|exists:shopping_cart,id',
            'items.*.quantity' => 'sometimes|required|integer|min:1'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['id']) && isset($data['quantity'])) {
            $cartItem = Cart::findOrFail($data['id']); 
            $cartItem->update(['quantity' => $data['quantity']]);
            
            return ['message' => 'Item updated successfully!'];
        }

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $cartItem = Cart::findOrFail($item['id']); 
                $cartItem->update(['quantity' => $item['quantity']]);
            }
            
            return ['message' => 'Items updated successfully!'];
        }

        throw new \Exception('Invalid update data provided.');
    }


    public function deleteFromCart($data)
    {
        $validator = Validator::make($data, [
            'ids.*' => 'required|integer|exists:shopping_cart,id'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $ids = isset($data['id']) ? [$data['id']] : $data['ids'];
        Cart::destroy($ids);

        return ['message' => 'Items removed from cart successfully!'];
    }
}
