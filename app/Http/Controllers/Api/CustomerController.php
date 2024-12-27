<?php

// app/Http/Controllers/Api/CustomerController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('customers', 'public');
            $validated['image'] = $imagePath;
        }

        $customer = Customer::create($validated);
        $customer->image_url = $customer->image ? Storage::url($customer->image) : null;

        return response()->json($customer, Response::HTTP_CREATED);
    }

    public function show(Customer $customer)
    {
        $customer->image_url = $customer->image ? Storage::url($customer->image) : null;
        return response()->json($customer, Response::HTTP_OK);
    }



    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'image' => 'nullable|string', // Can accept Base64 string or file path
            'status' => 'nullable|boolean',
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

                $imageName = 'customers/' . uniqid() . '.' . $extension;

                // Save new image
                Storage::disk('public')->put($imageName, $imageData);
                $validated['image'] = $imageName;

                // Delete old image if exists
                if ($customer->image) {
                    Storage::disk('public')->delete($customer->image);
                }
            } 
            // Check if the image is uploaded as a file
            elseif ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('customers', 'public');
                $validated['image'] = $imagePath;

                // Delete old image if exists
                if ($customer->image) {
                    Storage::disk('public')->delete($customer->image);
                }
            }
        }

        // Update customer with validated data
        $customer->update($validated);

        // Add image URL for response
        $customer->image_url = $customer->image ? Storage::url($customer->image) : null;

        return response()->json($customer, Response::HTTP_OK);
    }


    public function destroy(Customer $customer)
    {
        if ($customer->image) {
            Storage::disk('public')->delete($customer->image);
        }

        $customer->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
