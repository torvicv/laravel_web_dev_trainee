<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Depends;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class ProductControllerTest extends TestCase
{
    /**
     * Test return data in valid format.
     */
    public function testIndexReturnDataInValidFormat(): void {

        $user = User::where('email', 'admin@gmail.com')->first();

        Sanctum::actingAs($user, ['*']);

        $this->getJson('api/products')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [ '*' =>
                    [
                        'name',
                        'description',
                        'price'
                    ]
                ]
            );
    }

    public function testIndexReturnFiveProducts() {
        $user = User::where('email', 'admin@gmail.com')->first();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('api/products')
            ->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count(json_decode($response->original)) === 5);
    }

    public function testPostProductReturnOKAndProductEqualsToPost() {
        $user = User::where('email', 'admin@gmail.com')->first();

        Sanctum::actingAs($user, ['*']);

        $product['name'] = 'IPHONE 15';
        $product['description'] = 'IPhone 15 description.';
        $product['price'] = 799.9;

        $response = $this->postJson('api/products', $product)
            ->assertStatus(Response::HTTP_OK);

        assertEquals($response->original->name, 'IPHONE 15');
        assertEquals($response->original->description, 'IPhone 15 description.');
        assertEquals($response->original->price, 799.9);
    }

    #[Depends('testPostProductReturnOKAndProductEqualsToPost')]
    public function testShowLastProductInsertedAndCheckIfIsEqual() {
        $user = User::where('email', 'admin@gmail.com')->first();

        Sanctum::actingAs($user, ['*']);

        $product = Product::latest()->first();

        $response = $this->getJson('api/products/'.$product->id)
            ->assertStatus(Response::HTTP_OK);

        assertEquals($response->original->name, $product->name);
        assertEquals($response->original->description, $product->description);
        assertEquals($response->original->price, $product->price);
    }

    public function testUpdateLastProductInsertedAndCheckIfIsEqual() {
        $user = User::where('email', 'admin@gmail.com')->first();

        Sanctum::actingAs($user, ['*']);

        $productId = Product::latest()->first();
        $product = Product::latest()->first();
        $product->name = 'IPHONE 15';

        $response = $this->putJson('api/products/'.$productId->id, $product->toArray())
            ->assertStatus(Response::HTTP_OK);

        assertEquals($response->original->name, $product->name);
        assertEquals($response->original->description, $productId->description);
        assertEquals($response->original->price, $productId->price);
    }

    public function testDeleteLastProductInsertedAndCheckIfReturnTextDeleted() {
        $user = User::where('email', 'admin@gmail.com')->first();

        Sanctum::actingAs($user, ['*']);

        $product = Product::latest()->first();

        $response = $this->deleteJson('api/products/'.$product->id)
            ->assertStatus(Response::HTTP_OK);

        assertEquals($response->original['response'], 'Deleted');
    }
}
