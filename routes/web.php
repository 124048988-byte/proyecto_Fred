<!-- ESTE CÓDIGO ME LO DIÓ CHAT  - FER -->





<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(); // si usaste Breeze o Auth scaffolding

Route::middleware(['auth'])->group(function () { //TENGO DUDA EN SI DEJAR ESTA LINEA POR EL "AUTH"
    //significa: solo usuarios que estén logueados pueden acceder a estas rutas.
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{id}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel');
});


//Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');




