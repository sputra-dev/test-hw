<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function loanBooks(Request $request)
    {
        try {
            $page = $request->query('page') ?? 1;
            $length = $request->query('length') ?? 10;

            $borrowedBooks = Book::where('status', 'false')
                ->whereNull('deleted_at')
                ->paginate($length, ['id', 'publisher', 'status'], 'page', $page);

            $responseData = [
                'message' => 'List of Borrowed Books',
                'data' => $borrowedBooks,
            ];

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving borrowed books: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'title' => 'required|string',
            'cover' => 'nullable|string',
            'author' => 'required|string',
            'publisher' => 'required|string',
            'publish_year' => 'required',
            'status' => 'string',
        ]);

        try {
            $bookData = $request->only([
                'category_id',
                'title',
                'author',
                'publisher',
                'publish_year',
            ]);

            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverContent = base64_encode(file_get_contents($cover->path()));
                $bookData['cover'] = $coverContent;
            } elseif ($request->filled('cover')) {
                $bookData['cover'] = $request->input('cover');
            }

            $book = Book::create($bookData);

            $responseData = [
                'message' => 'Book Added Successfully',
                'data' => [
                    'id' => $book->id,
                    'category_id' => $book->category_id,
                    'title' => $book->title,
                ],
            ];

            return response()->json($responseData, 201);
        } catch (\Exception $e) {
            Log::error('Error creating book: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'title' => 'required|string',
            'cover' => 'nullable|string',
            'author' => 'required|string',
            'publisher' => 'required|string',
            'publish_year' => 'required',
            'status' => 'string',
        ]);

        try {
            $book = Book::findOrFail($id);

            $bookData = $request->only([
                'category_id',
                'title',
                'author',
                'publisher',
                'publish_year',
                'status'
            ]);

            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverContent = base64_encode(file_get_contents($cover->path()));
                $bookData['cover'] = $coverContent;
            } elseif ($request->filled('cover')) {
                $bookData['cover'] = $request->input('cover');
            }

            $book->update($bookData);

            $responseData = [
                'message' => 'Book Updated Successfully',
                'data' => [
                    'id' => $book->id,
                    'category_id' => $book->category_id,
                    'title' => $book->title,
                ],
            ];

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            Log::error('Error updating book: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $book = Book::withTrashed()->findOrFail($id);

            if ($book->trashed()) {
                return response()->json(['message' => 'Book has been deleted'], 200);
            }

            $book->delete();

            return response()->json(['message' => 'Book deleted'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting book: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
