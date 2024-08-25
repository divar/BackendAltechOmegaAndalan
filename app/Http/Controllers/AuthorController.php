<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuhtorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $author = Author::select("*");

        if ($request->get('search', false)) {
            $author = $author->whereFullText('bio', 'like', '%' . $request->get('search') . '%');
        }

        if ($request->get('name', false)) {
            $author = $author->where('name', 'like', $request->get('name') . '%');
        }

        if ($request->get('birth_date', false)) {
            $author = $author->where('birth_date', $request->get('birth_date'));
        }

        $author = $author->paginate(15);

        if ($request->get('load_relation', false)) {
            $author->load("book");
        }
        return response()->json($author, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuhtorRequest $request)
    {
        Log::info('Author Store method accessed.', $request->all());
        $validated = $request->validated();

        $author = Author::create($validated);

        return response()->json(['message' => 'author created', 'data' => $author], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cacheKey = "author_{$id}";
        $author = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($id) {
            return Author::findOrFail($id);
        });
        $author = $author->load('book');
        return response()->json($author, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        Log::info('Author Store method accessed.', $request->all());
        $validated = $request->validated();

        $author->update($validated);

        return response()->json(['message' => 'Author updated', 'data' => $author], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json(['message' => 'Author deleted', 'data' => $author], 200);
    }

    public function association($authorId){
        $author = Author::find($authorId);

        if ($author === null) {
            return response()->json(['message' => 'no author found for the author id', 'author_id' => $authorId], 404);
        }

        return response()->json($author->book()->paginate(4), 200);
    }
}
