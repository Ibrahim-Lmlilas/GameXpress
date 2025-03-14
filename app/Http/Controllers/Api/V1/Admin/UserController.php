<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::findByName($request->role, 'web'); // Spécifiez le guard ici si nécessaire
        $user->assignRole($role);

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur créé avec succès',
            'data' => $user->load('roles')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'role' => 'sometimes|required|string|exists:roles,name',
        ]);

        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($request->filled('role')) {
            $role = Role::findByName($request->role, 'web'); 
            $user->syncRoles($role);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $user->load('roles')
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }
}
