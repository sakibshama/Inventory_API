<?php

// app/Http/Controllers/Api/SupplierController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return response()->json($suppliers, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('suppliers', 'public');
            $validated['image'] = $imagePath;
        }

        $supplier = Supplier::create($validated);
        $supplier->image_url = $supplier->image ? Storage::url($supplier->image) : null;

        return response()->json($supplier, Response::HTTP_CREATED);
    }

    public function show(Supplier $supplier)
    {
        $supplier->image_url = $supplier->image ? Storage::url($supplier->image) : null;
        return response()->json($supplier, Response::HTTP_OK);
    }


    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'image' => 'nullable|string', // Can accept Base64 string or file path
        ]);

        // Handle image field
        if (isset($validated['image'])) {
            $imageData = $validated['image'];

            // Check if the image is a Base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $extension = $matches[1];
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    return response()->json(['error' => 'Invalid image data'], Response::HTTP_BAD_REQUEST);
                }

                $imageName = 'suppliers/' . uniqid() . '.' . $extension;

                // Save new image
                Storage::disk('public')->put($imageName, $imageData);
                $validated['image'] = $imageName;

                // Delete old image if exists
                if ($supplier->image) {
                    Storage::disk('public')->delete($supplier->image);
                }
            } 
            // Check if the image is uploaded as a file
            elseif ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('suppliers', 'public');
                $validated['image'] = $imagePath;

                // Delete old image if exists
                if ($supplier->image) {
                    Storage::disk('public')->delete($supplier->image);
                }
            }
        }

        // Update supplier with validated data
        $supplier->update($validated);

        // Add image URL for response
        $supplier->image_url = $supplier->image ? Storage::url($supplier->image) : null;

        return response()->json($supplier, Response::HTTP_OK);
    }


    public function destroy(Supplier $supplier)
    {
        if ($supplier->image) {
            Storage::disk('public')->delete($supplier->image);
        }

        $supplier->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
