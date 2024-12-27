<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AllUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AllUserController extends Controller
{
    public function index()
    {
        return AllUser::with('role')->get();
    }

    // public function store(Request $request)
    // {
    //     Log::info($request);

    //     $validatedData = $request->validate([
    //         'role_id' => 'required|exists:roles,id',
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:8',
    //         'gender' => 'required|string',
    //         'phone' => 'nullable|string|max:15',
    //         'c_percentage' => 'numeric|between:0,100',
    //         'c_amount' => 'numeric|min:0',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'status' => 'boolean',
    //     ]);
    
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('all_users', 'public');
    //         $validatedData['image'] = $imagePath;
    //     }
    
    //     // $validatedData['password'] = Hash::make($validatedData['password']);
    
    //     $allUser = AllUser::create($validatedData);
    //     // $allUser->image_url = $allUser->image ? Storage::url($allUser->image) : null;
    
    //     return response()->json($allUser, Response::HTTP_CREATED);
    // }

    public function store(Request $request)
{
    try {
        // Log::info('Incoming request: ', $request->all());

        $validatedData = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string',
            'password' => 'required|string|min:8',
            'gender' => 'required|string',
            'phone' => 'nullable|string|max:15',
            'c_percentage' => 'numeric|between:0,100',
            'c_amount' => 'numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('all_users', 'public');
            $validatedData['image'] = $imagePath;
        }

        $validatedData['password'] = Hash::make($validatedData['password']);

        $allUser = AllUser::create($validatedData);

        return response()->json($allUser, Response::HTTP_CREATED);

    } catch (ValidationException $e) {
        // Log::error('Validation error: ', $e->errors());
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        // Log::error('Unexpected error: ', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'An unexpected error occurred'], 500);
    }
}

    




    public function show(AllUser $allUser)
    {
        return $allUser->load('role');
    }



    public function update(Request $request, AllUser $allUser)

{
    
    $validated = $request->validate([
        'role_id' => 'nullable|exists:roles,id',
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|string',
        'password' => 'nullable|string|min:8',
        'gender' => 'nullable|string',
        'phone' => 'nullable|string|max:15',
        'c_percentage' => 'nullable|numeric|between:0,100',
        'c_amount' => 'nullable|numeric|min:0',
        'image' => 'nullable|string', // Can accept Base64 string or file path
        'status' => 'nullable|boolean',
    ]);


    // Hash the password if provided
    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

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

            $imageName = 'all_users/' . uniqid() . '.' . $extension;

            // Save new image
            Storage::disk('public')->put($imageName, $imageData);
            $validated['image'] = $imageName;

            // Delete old image if exists
            if ($allUser->image) {
                Storage::disk('public')->delete($allUser->image);
            }
        } 
        // Check if the image is uploaded as a file
        elseif ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('all_users', 'public');
            $validated['image'] = $imagePath;

            // Delete old image if exists
            if ($allUser->image) {
                Storage::disk('public')->delete($allUser->image);
            }
        }
    }

    // Update the user with validated data
    $allUser->update($validated);

    // Add image URL for the response
    // $allUser->image_url = $allUser->image ? Storage::url($allUser->image) : null;
    Log::info($allUser);

    return response()->json($allUser, Response::HTTP_OK);
}


    public function destroy(AllUser $allUser)
    {
        $allUser->delete();

        return response()->json(null, 204);
    }
}
