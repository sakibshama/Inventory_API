<?php

// app/Http/Controllers/Api/BrandController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all()->map(function ($brand) {
            $brand->logo_url = $brand->logo_url;  // Triggers the accessor
            return $brand;
        });

        return response()->json($brands, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands', 'public');
            $validated['logo'] = $logoPath;
        }

        $brand = Brand::create($validated);
   

        return response()->json($brand, Response::HTTP_CREATED);
    }

    public function show(Brand $brand)
    {
        // $brand->logo_url = $brand->logo_url;  // Triggers the accessor

        return response()->json($brand, Response::HTTP_OK);
    }






    public function update(Request $request, Brand $brand)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'logo' => 'nullable|string', // Accept Base64 string or a file path
        ]);

        // Check if the logo is provided as Base64 or as a file
        if (isset($validated['logo'])) {
            $logoData = $validated['logo'];

            // If the logo is a Base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $logoData, $matches)) {
                // Extract the file extension
                $extension = $matches[1];
                $logoData = substr($logoData, strpos($logoData, ',') + 1);

                // Decode the Base64 string
                $logoData = base64_decode($logoData);

                if ($logoData === false) {
                    return response()->json(['error' => 'Invalid image data'], Response::HTTP_BAD_REQUEST);
                }

                // Generate a unique filename
                $logoName = 'brands/' . uniqid() . '.' . $extension;

                // Save the image to the storage (public disk)
                Storage::disk('public')->put($logoName, $logoData);
                $validated['logo'] = $logoName;

                // Delete the old logo if it exists
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }
            } 
            // If the logo is uploaded as a file
            elseif ($request->hasFile('logo')) {
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }

                $logoPath = $request->file('logo')->store('brands', 'public');
                $validated['logo'] = $logoPath;
            }
        }

        // Update the brand with validated data
        if (isset($validated['name'])) $brand->name = $validated['name'];
        if (isset($validated['logo'])) $brand->logo = $validated['logo'];

        $brand->save();

        return response()->json($brand, Response::HTTP_OK);
    }


    public function destroy(Brand $brand)
    {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
