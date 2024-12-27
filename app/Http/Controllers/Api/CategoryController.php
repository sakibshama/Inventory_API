<?php

// app/Http/Controllers/Api/CategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all(), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        $category = Category::create($validated);

        return response()->json($category, Response::HTTP_CREATED);
    }




    public function show(Category $category)
    {
        return response()->json($category, Response::HTTP_OK);
    }








    public function update(Request $request, $categoryId)
    {

        // Fetch the category by ID
        $category = Category::findOrFail($categoryId);

        // Validate the request data
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|string', // Accept Base64 string or a file path
        ]);

        // Check if the image is provided as Base64 or as a file
        if (isset($validated['image'])) {
            $imageData = $validated['image'];

            // If the image is a Base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                // Extract the file extension
                $extension = $matches[1];
                $imageData = substr($imageData, strpos($imageData, ',') + 1);

                // Decode the Base64 string
                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    return response()->json(['error' => 'Invalid image data'], Response::HTTP_BAD_REQUEST);
                }

                // Generate a unique filename
                $imageName = 'categories/' . uniqid() . '.' . $extension;
                
                // Save the image to the storage (public disk)
                Storage::disk('public')->put($imageName, $imageData);
                $validated['image'] = $imageName;

                // Delete the old image if it exists
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
            } 
            // If the image is uploaded as a file
            elseif ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
                $validated['image'] = $imagePath;

                // Delete old image if it exists
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
            }
        }

        // Update the category with validated data
        $category->update($validated);

        return response()->json($category, Response::HTTP_OK);
    }


    

    public function destroy(Category $category)
    {
        // Delete associated image file if it exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
