<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // NECESARIO para manejar la foto
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            // Carga el usuario con sus relaciones de departamento y rol
            'user' => $request->user()->load(['departamento', 'rol']),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Validar y llenar datos personales (name y email)
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // 2. Manejar la carga de la foto de perfil (Lógica ELIMINADA para volver a la versión anterior)
        if ($request->hasFile('foto_perfil')) {
        $file = $request->file('foto_perfil');
        //     // Almacena el archivo en 'storage/app/public/profile-photos'
        $path = $file->store('profile-photos', 'public'); 

        //     // Borra la foto anterior si existe
        if ($user->foto_perfil) {
         Storage::disk('public')->delete($user->foto_perfil);
        }

        //     // Guarda la nueva ruta en la base de datos
        $user->foto_perfil = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
