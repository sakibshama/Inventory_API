<?php

// app/Http/Controllers/Api/InvestmentController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class InvestmentController extends Controller
{
    public function index()
    {
        $investments = Investment::with(['paymentType'])->get();

        return response()->json($investments, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_name' => 'required|string|max:255',
            'payment_id' => 'required|exists:payment_types,id',
            'amount' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deed_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
        ]);

        // Handle image uploads
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('investments', 'public');
            $validated['image'] = $imagePath;
        }

        if ($request->hasFile('deed_image')) {
            $deedImagePath = $request->file('deed_image')->store('investments/deeds', 'public');
            $validated['deed_image'] = $deedImagePath;
        }

        $investment = Investment::create($validated);

        // Add image URLs for response
        $investment->image_url = $investment->image ? Storage::url($investment->image) : null;
        $investment->deed_image_url = $investment->deed_image ? Storage::url($investment->deed_image) : null;

        return response()->json($investment, Response::HTTP_CREATED);
    }

    public function show(Investment $investment)
    {
        // Add image URLs for response
        $investment->image_url = $investment->image ? Storage::url($investment->image) : null;
        $investment->deed_image_url = $investment->deed_image ? Storage::url($investment->deed_image) : null;

        return response()->json($investment, Response::HTTP_OK);
    }

    public function update(Request $request, Investment $investment)
    {

        Log::info($request);

        $validated = $request->validate([
            'investor_name' => 'nullable|string|max:255',
            'payment_id' => 'nullable|exists:payment_types,id',
            'amount' => 'nullable|numeric',
            'image' => 'nullable|string', // Accept Base64 string or file path
            'deed_image' => 'nullable|string', // Accept Base64 string or file path
            'status' => 'nullable|boolean',
        ]);

        // Handle image field for `image`
        if (isset($validated['image'])) {
            $imageData = $validated['image'];

            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $extension = $matches[1];
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    return response()->json(['error' => 'Invalid image data'], Response::HTTP_BAD_REQUEST);
                }

                $imageName = 'investments/' . uniqid() . '.' . $extension;
                Storage::disk('public')->put($imageName, $imageData);
                $validated['image'] = $imageName;

                if ($investment->image) {
                    Storage::disk('public')->delete($investment->image);
                }
            } elseif ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('investments', 'public');
                $validated['image'] = $imagePath;

                if ($investment->image) {
                    Storage::disk('public')->delete($investment->image);
                }
            }
        }

        // Handle image field for `deed_image`
        if (isset($validated['deed_image'])) {
            $deedImageData = $validated['deed_image'];

            if (preg_match('/^data:image\/(\w+);base64,/', $deedImageData, $matches)) {
                $extension = $matches[1];
                $deedImageData = substr($deedImageData, strpos($deedImageData, ',') + 1);
                $deedImageData = base64_decode($deedImageData);

                if ($deedImageData === false) {
                    return response()->json(['error' => 'Invalid deed image data'], Response::HTTP_BAD_REQUEST);
                }

                $deedImageName = 'investments/deeds/' . uniqid() . '.' . $extension;
                Storage::disk('public')->put($deedImageName, $deedImageData);
                $validated['deed_image'] = $deedImageName;

                if ($investment->deed_image) {
                    Storage::disk('public')->delete($investment->deed_image);
                }
            } elseif ($request->hasFile('deed_image')) {
                $deedImagePath = $request->file('deed_image')->store('investments/deeds', 'public');
                $validated['deed_image'] = $deedImagePath;

                if ($investment->deed_image) {
                    Storage::disk('public')->delete($investment->deed_image);
                }
            }
        }

        $investment->update($validated);

        // Add image URLs for response
        $investment->image_url = $investment->image ? Storage::url($investment->image) : null;
        $investment->deed_image_url = $investment->deed_image ? Storage::url($investment->deed_image) : null;

        return response()->json($investment, Response::HTTP_OK);
    }

    public function destroy(Investment $investment)
    {
        if ($investment->image) {
            Storage::disk('public')->delete($investment->image);
        }

        if ($investment->deed_image) {
            Storage::disk('public')->delete($investment->deed_image);
        }

        $investment->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
