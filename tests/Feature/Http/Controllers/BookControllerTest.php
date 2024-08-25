<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_an_book()
    {
        $author = Author::factory()->create();

        // payload to send to the controller
        $bookData = [
            'title'        => 'Sherlock Holmes',
            'description'  => 'An amazing description of a great book.',
            'publish_date' => '2022-01-01',
            'author_id'    => $author->id,
        ];

        // Act: Make a POST request to the 'books' endpoint
        $response = $this->postJson('/books', $bookData);

        // check response has successfully and has a correct data
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title'        => 'Sherlock Holmes',
                    'description'  => 'An amazing description of a great book.',
                    'publish_date' => '2022-01-01',
                    'author_id'    => $author->id,
                ],
            ]);

        // check the data has recorded in database
        $this->assertDatabaseHas('books', $bookData);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_an_book()
    {
        // Arrange: Create an empty payload
        $bookData = [];

        // Act: Make a POST request to the 'books' endpoint with an empty payload
        $response = $this->postJson('/books', $bookData);

        // Assert: Check if the response has validation errors
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'author_id'], $responseKey = 'errors');
    }

    /** @test */
    public function it_can_updating_an_book()
    {
        // Arrange: Create an author and a book
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        // payload to update
        $updatedData = [
            'title'        => 'Title Changed',
            'description'  => 'Updated description of the book.',
            'publish_date' => '2021-01-01',
            'author_id'    => $author->id,
        ];

        // Make a PUT request to the 'books' endpoint
        $response = $this->putJson("/books/{$book->id}", $updatedData);
        // check response has successfully and has a correct data
        $response->assertStatus(200);

        $response->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => 'Title Changed',
                    'description' => 'Updated description of the book.',
                    'publish_date' => '2021-01-01',
                    'author_id' => $author->id,
                ],
            ]);

        // check the data has recorded in database
        $this->assertDatabaseHas('books', $updatedData);
    }

    /** @test */
    public function it_validates_required_fields_when_updating_an_book()
    {
        //Create an author and a book
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        // Define an empty payload
        $updatedData = [];

        // Make a PUT request to update the book with empty data
        $response = $this->putJson("/books/{$book->id}", $updatedData);

        // Check the response status is 422
        $response->assertStatus(422);

        // Assert: Check for validation errors in the response
        $response->assertJsonValidationErrors(['title', 'author_id']);

    }

    /** @test */
    public function it_can_delete_a_book()
    {
        // Create an author and a book
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        // Make a DELETE request to the 'books' endpoint
        $response = $this->deleteJson("/books/{$book->id}");

        // Check that the response status is 200 (OK)
        $response->assertStatus(200);

        // Verify that the book has been deleted from the database
        $this->assertSoftDeleted($book);
    }
}
