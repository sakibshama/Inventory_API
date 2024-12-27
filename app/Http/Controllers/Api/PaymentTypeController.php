<?php

// app/Http/Controllers/Api/PaymentTypeController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $paymentTypes = PaymentType::all();
        return response()->json($paymentTypes, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'account_no' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Validate image
            'status' => 'boolean',
        ]);

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $paymentType = PaymentType::create($validated);
        return response()->json($paymentType, Response::HTTP_CREATED);
    }

    public function show(PaymentType $paymentType)
    {
        return response()->json($paymentType, Response::HTTP_OK);
    }

    // public function update(Request $request, PaymentType $paymentType)
    // {
    //     $validated = $request->validate([
    //         'type' => 'string',
    //         'account_no' => 'nullable|string',
    //         'logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Validate image
    //         'status' => 'boolean',
    //     ]);

    //     // Handle logo update if provided
    //     if ($request->hasFile('logo')) {
    //         // Delete the old logo if it exists
    //         if ($paymentType->logo) {
    //             Storage::disk('public')->delete($paymentType->logo);
    //         }
    //         $validated['logo'] = $request->file('logo')->store('logos', 'public');
    //     }

    //     $paymentType->update($validated);
    //     return response()->json($paymentType, Response::HTTP_OK);
    // }

    public function update(Request $request, PaymentType $paymentType)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'type' => 'required|string',
            'account_no' => 'nullable|string',
            'logo' => 'nullable|string', // Accept Base64 string or a file path
            'status' => 'nullable|boolean',
        ]);

        // Handle the logo field (Base64 or file upload)
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
                $logoName = 'logos/' . uniqid() . '.' . $extension;

                // Save the image to the storage (public disk)
                Storage::disk('public')->put($logoName, $logoData);
                $validated['logo'] = $logoName;

                // Delete the old logo if it exists
                if ($paymentType->logo) {
                    Storage::disk('public')->delete($paymentType->logo);
                }
            }
            // If the logo is uploaded as a file
            elseif ($request->hasFile('logo')) {
                // Delete the old logo if it exists
                if ($paymentType->logo) {
                    Storage::disk('public')->delete($paymentType->logo);
                }

                // Store the new logo file
                $validated['logo'] = $request->file('logo')->store('logos', 'public');
            }
        }

        // Update the payment type with validated data
        if (isset($validated['type'])) $paymentType->type = $validated['type'];
        if (isset($validated['account_no'])) $paymentType->account_no = $validated['account_no'];
        if (isset($validated['logo'])) $paymentType->logo = $validated['logo'];
        if (isset($validated['status'])) $paymentType->status = $validated['status'];

        $paymentType->save();

        // Return the updated payment type
        return response()->json($paymentType, Response::HTTP_OK);
    }


    public function destroy(PaymentType $paymentType)
    {
        // Delete the logo if it exists
        if ($paymentType->logo) {
            Storage::disk('public')->delete($paymentType->logo);
        }
        $paymentType->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
