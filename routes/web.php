<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController; // <-- ¡IMPORTACIÓN AÑADIDA!
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// GRUPO DE RUTAS PROTEGIDAS POR AUTENTICACIÓN
Route::middleware('auth')->group(function () {
    // 2. RUTAS DEL MÓDULO TICKETS
    // Route::resource genera las rutas index, create, store, show, edit, update, destroy
    Route::resource('tickets', TicketController::class);

    // --- RUTAS DE ACCIÓN PERSONALIZADAS AÑADIDAS PARA LA VISTA SHOW ---
    
    // Ruta para asignar el ticket al auxiliar logueado (Tomar Ticket)
    // PATCH se usa porque se está modificando un recurso existente (el ticket).
    Route::patch('tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');

    // Ruta para cambiar el estatus (ej. Marcar como Terminado)
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update_status');
    
    // Ruta para ver solo los tickets creados por el usuario (mis tickets)
    Route::get('/mis-tickets', [TicketController::class, 'misTickets'])->name('tickets.mine');
    
    // RUTA FALTANTE PARA AÑADIR COMENTARIOS (Resuelto)
    // POST se usa para crear un nuevo recurso (el comentario).
    Route::post('/tickets/{ticket}/comment', [TicketController::class, 'addComment'])->name('tickets.add_comment');

});

require __DIR__.'/auth.php';
