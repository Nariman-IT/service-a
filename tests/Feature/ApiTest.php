<?php

namespace Tests\Feature;


use App\Models\Api\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Api\Assortment;
use App\Models\User;
use App\Models\Api\Order;


class ApiTest extends TestCase
{

    public function test_assortment_route_returns_success()
    {
        $response = $this->getJson('api/assortment');
        $response->assertStatus(200)
                 ->assertJsonStructure();
    }

    public function test_my_order_requires_authentication()
    {
        $response = $this->getJson('api/myOrder');
        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }


    public function test_guest_can_add_to_cart()
    {
        $guest = Guest::create([
            'guest_id' => fake()->numberBetween(),
            'ip' => fake()->numberBetween()
        ]);

        $item = Assortment::create([
            'name' => fake()->name(),
            'price' => 1000,
            'type' => 'pizza',
            'description' => 'test description',
            'image_url' => 'test.image_url',
        ]);

        $response = $this->withHeaders([
            'X-GUEST-ID' => $guest->guest_id
        ])->postJson('api/cart/add', [
            'assortment_id' => $item->id,
            'quantity' => 1
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'cart']);
    }

 
    public function test_add_to_cart_requires_valid_item()
    {
        $response = $this->postJson('api/cart/add', [
            'assortment_id' => 999,
            'quantity' => 1
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['assortment_id']);
    }


    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }


    public function test_login_fails_with_wrong_credentials()
    {
        
        $response = $this->postJson('api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Неверные учетные данные']);
    }

 
    public function test_user_can_register()
    {
        $response = $this->postJson('api/register', [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['user', 'token']);
    }


    public function test_registration_requires_unique_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('api/register', [
            'name' => fake()->name(),
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }


    public function test_create_order_requires_authentication()
    {
        $response = $this->postJson('api/order');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_order_history()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('api/history');

        $response->assertStatus(200)
                 ->assertJsonStructure();
    }



    public function test_admin_can_create_item()
    {

        $admin = User::factory()->create(['is_admin' => true]);
        $token = $admin->createToken('auth-token')->plainTextToken;

   
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/admin/store', [
            'name' => fake()->name(),
            'price' => 1000,
            'type' => 'pizza',
            'description' => 'Classic pizza',
            'image_url' => 'https://example.com/pizza.jpg'
        ]);


        $response->assertStatus(201)
            ->assertJson([
                'success' => true
            ]);
    }


    public function test_non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('api/admin/list');

        $response->assertStatus(403);
    }

   
    public function test_admin_can_update_order_status()
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'cart_data' => json_encode(fake()->sentence()),
            'phone' => '123123123',	
            'email' => fake()->email(),
            'address' => 'test',
            'delivery_time' => 'testTime'
        ]);

        $admin = User::factory()->create(['is_admin' => true]);
        $token = $admin->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->patchJson('api/admin/order', [
            'id' => $order->id,
            'status' => 'completed'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}
