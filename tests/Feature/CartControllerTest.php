<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreAsLoggedUser()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', '/path/to/your/route', [
            'user_id' => $user->id,
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 2, 'quantity' => 3],
            ],
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success']);
        
        $this->assertCount(2, Cart::all());
    }
}

