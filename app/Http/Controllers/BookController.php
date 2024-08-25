<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $book = Book::select("books.*");

        if ($request->get('search', false)) {
            $book = $book->whereFullText('description', 'like', '%' . $request->get('search') . '%');
        }

        if ($request->get('title', false)) {
            $book = $book->where('title', 'like', $request->get('title') . '%');
        }

        if ($request->get('publish_date', false)) {
            $book = $book->where('publish_date', $request->get('publish_date'));
        }

        if ($request->get('author_name', false)) {
            $book = $book->join('authors', 'authors.id', 'books.author_id')
                ->where('authors.name', $request->get('author_name'));
        }

        $book = $book->paginate(15);

        if ($request->get('load_relation', false)) {
            $book->load("author");
        }
        return response()->json($book, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        Log::info('Book Store method accessed.', $request->all());
        $validated = $request->validated();

        $author = Author::find($validated['author_id']);
        $book = $author->book()->create($validated);

        return response()->json(['message' => 'Book created', 'data' => $book], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cacheKey = "book_{$id}";
        $book = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($id) {
            return Book::findOrFail($id);
        });
        $book = $book->load('author');
        return response()->json($book, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        Log::info('Book Store method accessed.', $request->all());
        $validated = $request->validated();

        $book->update($validated);

        return response()->json(['message' => 'Book updated', 'data' => $book], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Book deleted', 'data' => $book], 200);
    }
}
