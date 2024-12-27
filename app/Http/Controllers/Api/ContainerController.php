<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Container;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ContainerController extends Controller
{
 // Display a listing of containers
 public function index()
 {
     $containers = Container::all(); // Retrieve all containers
     return response()->json($containers);
 }

 // Store a newly created container in storage
 public function store(Request $request)
 {
     // Validate the incoming request
     $validated = $request->validate([
         'shipment_id' => 'required|integer',
         'amount' => 'nullable|numeric',
         'lc_copy' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         'status' => 'nullable|boolean', // Status is now a boolean
     ]);

     if ($request->hasFile('lc_copy')) {
        $imagePath = $request->file('lc_copy')->store('containers', 'public');
        $validated['lc_copy'] = $imagePath;
    }
     // Create a new container record
     $container = Container::create($validated);

     // Return the created container
     return response()->json($container, 201);
 }

 // Display the specified container
 public function show($id)
 {
     // Find the container by ID or fail
     $container = Container::findOrFail($id);
     return response()->json($container);
 }

 // Update the specified container in storage
//  public function update(Request $request, $containerId)
//  {
//      // Find the container by ID or fail
//      $container = Container::findOrFail($containerId);
 
//      // Validate the incoming request
//      $validated = $request->validate([
//          'shipment_id' => 'nullable|numeric',
//          'amount' => 'nullable|numeric',
//          'lc_copy' => 'nullable|string',
//      ]);
 
//      // Check if the image is provided as Base64 or as a file
//      if (isset($validated['lc_copy'])) {
//          $imageData = $validated['lc_copy'];
 
//          // If the image is a Base64 string
//          if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
//              // Extract the file extension
//              $extension = $matches[1];
//              $imageData = substr($imageData, strpos($imageData, ',') + 1);
 
//              // Decode the Base64 string
//              $imageData = base64_decode($imageData);
 
//              if ($imageData === false) {
//                  return response()->json(['error' => 'Invalid image data'], Response::HTTP_BAD_REQUEST);
//              }
 
//              // Generate a unique filename
//              $imageName = 'containers/' . uniqid() . '.' . $extension;
 
//              // Save the image to the storage (public disk)
//              Storage::disk('public')->put($imageName, $imageData);
//              $validated['lc_copy'] = $imageName;
 
//              // Delete the old image if it exists
//              if ($container->image) {
//                  Storage::disk('public')->delete($container->image);
//              }
//          } 
//          // If the image is uploaded as a file
//          elseif ($request->hasFile('lc_copy')) {
//              $imagePath = $request->file('lc_copy')->store('containers', 'public');
//              $validated['lc_copy'] = $imagePath;
 
//              // Delete old image if it exists
//              if ($container->image) {
//                  Storage::disk('public')->delete($container->image);
//              }
//          }
//      }
 
//      // Update the container with validated data
//      $container->update($validated);
 
//      return response()->json($container, Response::HTTP_OK);
//  }

public function update(Request $request, $containerId)
{

    // Find the container by ID or fail
    $container = Container::findOrFail($containerId);

    // Validate the incoming request
    $validated = $request->validate([
        'shipment_id' => 'nullable|numeric',
        'amount' => 'nullable|numeric',
        'lc_copy' => 'nullable|string', // Base64 string or file path
    ]);

    // Check if the lc_copy (image) is provided as Base64 or as a file
    if (isset($validated['lc_copy'])) {
        $imageData = $validated['lc_copy'];

        // If the lc_copy is a Base64 string
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
            $imageName = 'containers/' . uniqid() . '.' . $extension;

            // Save the image to the storage (public disk)
            Storage::disk('public')->put($imageName, $imageData);
            $validated['lc_copy'] = $imageName;

            // Delete the old image if it exists
            if ($container->lc_copy) {
                Storage::disk('public')->delete($container->lc_copy);
            }
        } 
        // If the lc_copy is uploaded as a file
        elseif ($request->hasFile('lc_copy')) {
            $imagePath = $request->file('lc_copy')->store('containers', 'public');
            $validated['lc_copy'] = $imagePath;

            // Delete old image if it exists
            if ($container->lc_copy) {
                Storage::disk('public')->delete($container->lc_copy);
            }
        }
    }

    // Update the container with validated data
    $container->update($validated);

    return response()->json($container, Response::HTTP_OK);
}

 // Remove the specified container from storage
 public function destroy($id)
 {
     // Find the container by ID or fail
     $container = Container::findOrFail($id);
     
     // Delete the container
     $container->delete();

     return response()->json(null, 204); // Return a 204 No Content response
 }
 
}
