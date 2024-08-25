<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authorIds = Author::pluck('id'); // Fetch existing UUIDs from authors table

        Book::factory()->count(30000)->make()->each(function ($book) use ($authorIds) {
            $book->author_id = $authorIds->random();
            $book->save();
        });
    }
}
