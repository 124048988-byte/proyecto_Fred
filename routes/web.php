<?php

use App\Http\Controllers\ProfileController; // ¡Importación corregida!
use App\Http\Controllers\TicketController; 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;     // Importación de Auth para usar Auth::check()
use Illuminate\Support\Facades\Redirect; // Importación de Redirect para usar Redirect::route()

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar rutas web para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro de un grupo
| que contiene el middleware "web".
|
*/

// Ruta principal que carga la vista de bienvenida
Route::get('/', function () {
    if (Auth::check()) {
        return Redirect::route('dashboard');
    }
    return view('welcome');
});

// Ruta del dashboard (requiere autenticación y verificación de email)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// GRUPO DE RUTAS PROTEGIDAS POR AUTENTICACIÓN
Route::middleware('auth')->group(function () {

    //1. RUTAS DEL MÓDULO PERFIL DE USUARIO
    // Ruta para editar el perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // 2. RUTAS DEL MÓDULO TICKETS
    // Rutas RESTful estándar (index, create, store, show, edit, update)
    // Excluimos 'destroy' para asegurar que la única forma de "eliminar" sea cancelar.
    Route::resource('tickets', TicketController::class)->except(['destroy']);

    // --- RUTAS DE ACCIÓN PERSONALIZADAS PARA TICKETS ---
    
    // RUTA NUEVA: Para cambiar el estatus a 'cancelado'
    Route::patch('tickets/{ticket}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel');

    // Ruta para asignar el ticket al auxiliar logueado (Tomar Ticket)
    Route::patch('tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');

    // Ruta para cambiar el estatus (ej. Marcar como Terminado/Cerrado)
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update_status');
    
    // Ruta para ver solo los tickets creados por el usuario logueado
    Route::get('/mis-tickets', [TicketController::class, 'misTickets'])->name('tickets.mine');

    // RUTA PARA AÑADIR COMENTARIOS
    Route::post('tickets/{ticket}/comment', [TicketController::class, 'addComment'])->name('tickets.add_comment');

});

// Carga las rutas de autenticación (login, register, reset, etc.)
require __DIR__.'/auth.php';