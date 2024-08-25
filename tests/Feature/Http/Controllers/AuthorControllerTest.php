<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_can_create_an_author()
    {
        // payload to send to the controller
        $authorData = [
            'name' => 'Budiman',
            'bio' => 'Budi is a fictional author created for testing purposes.',
            'birth_date' => '1980-01-01',
        ];

        // post request to the 'authors' endpoint
        $response = $this->postJson('/authors', $authorData);

        // check response has successfully and has a correct data
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Budiman',
                    'bio' => 'Budi is a fictional author created for testing purposes.',
                    'birth_date' => '1980-01-01',
                ],
            ]);

        // check the data has recorded in database
        $this->assertDatabaseHas('authors', $authorData);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_an_author()
    {
        // Arrange: Create an empty payload
        $authorData = [];

        // POST request to the 'authors' endpoint with an empty payload
        $response = $this->postJson('/authors', $authorData);

        // Check if the response has validation errors
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name'],$responseKey = 'errors');
    }

    /** @test */
    public function it_can_updating_an_author()
    {
        // create an author
        $author = Author::factory()->create();

        // define payload to update
        $updatedData = [
            'name' => 'Yoyo',
            'bio' => 'Updated bio of the author.',
            'birth_date' => '1990-01-01',
        ];

        // make a put request to the 'authors' endpoint
        $response = $this->putJson("/authors/{$author->id}", $updatedData);

        // check response has successfully and has a correct data
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => $author->id,
                    'name' => 'Yoyo',
                    'bio' => 'Updated bio of the author.',
                    'birth_date' => '1990-01-01',
                ],
            ]);

        // check the data has recorded in database
        $this->assertDatabaseHas('authors', $updatedData);
    }

    /** @test */
    public function it_validates_required_fields_when_updating_an_author()
    {
        //create an author
        $author = Author::factory()->create();

        //define an empty payload
        $updatedData = [];

        //make a PUT request to update the author with empty data
        $response = $this->putJson("/authors/{$author->id}", $updatedData);

        //check that the response status is 422 (Unprocessable Entity)
        $response->assertStatus(422);

        //check for validation errors in the response
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_can_delete_an_author()
    {
        //Create an author
        $author = Author::factory()->create();

        //Make a DELETE request to the 'authors' endpoint
        $response = $this->deleteJson("/authors/{$author->id}");

        //Check that the response status is 200 (OK)
        $response->assertStatus(200);

        //Verify that the author has been deleted from the database
        $this->assertSoftDeleted($author);
    }
}
