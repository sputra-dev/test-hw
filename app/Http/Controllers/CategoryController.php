<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories',
            'note' => 'nullable|string',
        ]);

        try {
            $category = Category::create([
                'name' => $request->name,
                'note' => $request->note,
            ]);

            return response()->json(['message' => 'Category Added Successfully', 'data' => $category], 201);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $id,
            'note' => 'nullable|string',
        ]);

        try {
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name,
                'note' => $request->note,
            ]);

            return response()->json(['message' => 'Category Updated Successfully', 'data' => $category], 201);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function delete($id)
    {
        try {
            $category = Category::withTrashed()->findOrFail($id);

            if ($category->trashed()) {
                return response()->json(['message' => 'Category has been deleted'], 200);
            }

            $category->delete();

            return response()->json(['message' => 'Category deleted'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
