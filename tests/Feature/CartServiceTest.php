<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $cartService;

    public function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(CartService::class);
    }

    public function testStoreForLoggedUser()
    {
        $user = User::factory()->create();

        $response = $this->cartService->storeForLoggedUser([
            'user_id' => $user->id,
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 2, 'quantity' => 3],
            ],
        ]);

        $this->assertEquals('success', $response['status']);
        $this->assertCount(2, Cart::all());
    }

    public function testStoreForLoggedUserWithInvalidData()
    {
        $this->expectException(ValidationException::class);
        $this->cartService->storeForLoggedUser([
            'user_id' => null,
        ]);
    }

}

