<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Cache::rememberForever('books',function()
        {
            return Book::all();
        });
        dd($books->pluck('title'));
        return response()->json(Book::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string',
                'author' => 'required|string',
                'published_year' => 'required|integer',
                'genre' => 'required|string',
            ]
        );
        $book=Book::create($request->all());
        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Cache::store('redis')->get('book:' . $id);
        dd($book->title);
        
        // if ($book) {
        //     return response()->json($book, 200);
        // } else {
        //     return response()->json(['message' => 'Book not found'], 404);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);
        if(!$book)
        {
            return response()->json(['message' => 'Book not found'], 404);
        }
        $request->validate(
            [
                'title' => 'required|string',
                'author' => 'required|string',
                'published_year' => 'required|integer',
                'genre' => 'required|string',
            ]
        );
        $book->update($request->all());
        return response()->json($book, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['message' => 'Book not found'], 404);
        }
        $book->delete();
        return response()->json(['message' => 'Book deleted'], 200);
    }
}
