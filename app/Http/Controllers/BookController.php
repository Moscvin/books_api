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
        // Cachează lista de cărți permanent până la actualizare manuală
        $books = Cache::store('redis')->rememberForever('books', function() {
            return Book::all();
        });

        return response()->json($books, 200);
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

        // Creează și salvează o carte nouă în baza de date
        $book = Book::create($request->all());

        // Cachează cartea nou creată și actualizează cache-ul listei de cărți
        Cache::store('redis')->forever('book:' . $book->id, $book); // Cachează permanent cartea
        Cache::store('redis')->forget('books'); // Șterge cache-ul listei pentru a se reînnoi la următoarea cerere

        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Încearcă să obții cartea din cache, altfel o caută în baza de date
        $book = Cache::store('redis')->rememberForever('book:' . $id, function () use ($id) {
            return Book::find($id);
        });

        if ($book) {
            return response()->json($book, 200);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
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

        // Actualizează cartea în baza de date
        $book->update($request->all());

        // Actualizează cache-ul pentru cartea specifică și pentru lista de cărți
        Cache::store('redis')->forever('book:' . $book->id, $book); // Re-cachează cartea permanent
        Cache::store('redis')->forget('books'); // Șterge cache-ul listei pentru a fi reîmprospătat

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

        // Șterge cartea din baza de date și din cache
        $book->delete();
        Cache::store('redis')->forget('book:' . $id); // Șterge cartea din cache
        Cache::store('redis')->forget('books'); // Șterge cache-ul listei pentru reîmprospătare

        return response()->json(['message' => 'Book deleted'], 200);
    }
}
