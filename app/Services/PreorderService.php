<?php

namespace App\Services;

use App\Models\User;
use App\Models\Preorder;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class PreorderService
{
    public function processPreorderRequest($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|regex:/^0\d{10}$/',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $preorderData = [
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'buyer_name' => $data['name'],
            'buyer_lastname' => $data['lastname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => 'pending', // default status.
        ];

        return Preorder::create($preorderData);
    }
}
