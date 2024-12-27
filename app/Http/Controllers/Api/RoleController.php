<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{


    public function index()
    {
        return Role::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'permissions' => 'required|integer',
            'status' => 'boolean',
        ]);

        $role = Role::create($validated);

        return response()->json($role, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'role_name' => 'string|max:255',
            'permissions' => 'integer',
            'status' => 'boolean',
        ]);
    
        $role->update($validated);
    
        return response()->json($role, Response::HTTP_OK);
    }
    

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(null, 204);
    }
}
