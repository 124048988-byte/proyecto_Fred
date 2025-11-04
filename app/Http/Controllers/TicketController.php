<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Comentario; // Importación del modelo de comentarios
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log; // Importación para el manejo de errores

class TicketController extends Controller
{
    /**
     * Muestra la lista de todos los tickets.
     */
    public function index(): View
    {
        // Traer todos los tickets con sus relaciones (creador y auxiliar)
        $tickets = Ticket::with(['creador', 'auxiliar'])->latest()->get();
        
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Muestra el formulario para crear un nuevo ticket.
     */
    public function create(): View
    {
        return view('tickets.create');
    }

    /**
     * Almacena un nuevo ticket en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validación de datos
        $request->validate([
            'title' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
        ]);

        // 2. Creación del ticket
        Ticket::create([
            'title' => $request->title,
            'descripcion' => $request->descripcion,
            'estatus' => 'pendiente',
            'usuario_id' => Auth::id(), // Asigna al usuario logueado como creador
            'auxiliar_id' => null, // Inicialmente no está asignado
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket creado exitosamente.');
    }

    /**
     * Muestra el detalle de un ticket específico.
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\View\View
     */
    public function show(Ticket $ticket): View
    {
        // Carga la relación 'comentarios.creador' para evitar errores y tener los datos a mano.
        $ticket->load('comentarios.creador');
        
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Muestra el formulario para editar el ticket especificado.
     */
    public function edit(Ticket $ticket): View
    {
        // Implementar lógica de autorización aquí (e.g., solo creador si está pendiente)
        // Por ahora, solo muestra la vista.
        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Actualiza el ticket especificado en el almacenamiento.
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        // 1. Implementar lógica de autorización aquí (e.g., solo creador si está pendiente)

        // 2. Validación de datos
        $request->validate([
            'title' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
        ]);
        
        $ticket->update($request->only(['title', 'descripcion']));

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket actualizado exitosamente.');
    }


    /**
     * Elimina el ticket especificado del almacenamiento.
     * Solo permite eliminar si el usuario autenticado es el creador, el ticket está 'pendiente', y no tiene auxiliar asignado.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        // 1. Verificar si el usuario autenticado es el creador
        if (Auth::id() !== $ticket->usuario_id) {
            return redirect()->route('tickets.index')
                             ->with('error', 'No tienes permiso para eliminar este ticket.');
        }

        // 2. Verificar que el estatus sea 'pendiente' Y 3. Que no haya auxiliar asignado
        if ($ticket->estatus !== 'pendiente' || !is_null($ticket->auxiliar_id)) {
            return redirect()->route('tickets.show', $ticket)
                             ->with('error', 'Solo se pueden eliminar tickets pendientes y sin asignar.');
        }

        try {
            $ticket->delete();
            return redirect()->route('tickets.index')
                             ->with('success', 'Ticket eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar ticket: ' . $e->getMessage()); 
            return redirect()->back()->with('error', 'Ocurrió un error al intentar eliminar el ticket.');
        }
    }

    // =========================================================================
    // MÉTODOS DE ACCIÓN PERSONALIZADA
    // =========================================================================

    /**
     * Asigna el ticket al usuario auxiliar actualmente logueado.
     * Esto también cambia el estatus a 'en progreso'.
     *
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign(Ticket $ticket): RedirectResponse
    {
        // 1. Autorización: Se asume que Rol ID 1 = Jefe, 2 = Auxiliar, 3 = Usuario
        // Solo Jefes o Auxiliares pueden tomar tickets.
        $usuario = Auth::user();
        if ($usuario->rol_id === 3) { 
            return redirect()->route('tickets.show', $ticket)
                             ->with('error', 'Solo Jefes o Auxiliares pueden tomar tickets.');
        }

        // 2. Validación de Estado: Solo se puede tomar si está 'pendiente' y no asignado.
        if ($ticket->estatus !== 'pendiente' || !is_null($ticket->auxiliar_id)) {
            return redirect()->route('tickets.show', $ticket)
                             ->with('error', 'El ticket ya ha sido tomado o no está pendiente.');
        }

        // 3. Asignación y Actualización de Estado
        $ticket->auxiliar_id = Auth::id();
        $ticket->estatus = 'en progreso';
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)
                         ->with('success', 'Ticket #' . $ticket->id . ' tomado exitosamente. Estatus: En progreso.');
    }

    /**
     * Cambia el estatus del ticket a 'cerrado' (Terminado).
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $usuario = Auth::user();
        // 1. Autorización: Solo el auxiliar asignado (o Jefe) puede cerrar el ticket.
        // Si el usuario no es el auxiliar asignado Y no es Jefe (rol_id=1)
        if (Auth::id() !== $ticket->auxiliar_id && $usuario->rol_id !== 1) { 
            return redirect()->route('tickets.show', $ticket)
                             ->with('error', 'Solo el auxiliar asignado o un Jefe puede marcar este ticket como Terminado.');
        }
        
        // 2. Validación de Estado: No se puede cerrar si ya está cerrado.
        if ($ticket->estatus === 'cerrado') {
             return redirect()->route('tickets.show', $ticket)
                              ->with('error', 'El ticket ya estaba marcado como Terminado.');
        }

        // 3. Actualización de Estado
        // El estatus podría venir del request en un entorno real, pero aquí lo forzamos a 'cerrado'
        $ticket->estatus = 'cerrado';
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)
                         ->with('success', 'Ticket #' . $ticket->id . ' marcado como Terminado (Cerrado) exitosamente.');
    }
    
    /**
     * Añade un nuevo comentario al ticket.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addComment(Request $request, Ticket $ticket): RedirectResponse
    {
        // 1. Validación de la entrada
        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        // 2. Creación del Comentario
        $comentario = new Comentario();
        $comentario->contenido = $request->input('contenido');
        $comentario->user_id = Auth::id();
        $comentario->ticket_id = $ticket->id;
        $comentario->save();

        // 3. Lógica Opcional: Si un auxiliar/jefe comenta un ticket pendiente, lo asigna y lo pone en progreso.
        $usuario = Auth::user();
        // Asumiendo que rol_id 1 = Jefe, 2 = Auxiliar, 3 = Usuario (Solo soporte tiene rol_id 1 o 2)
        if ($usuario->rol_id !== 3) { 
            if ($ticket->estatus === 'pendiente') {
                $ticket->estatus = 'en progreso';
                // Asigna el auxiliar si no estaba asignado
                if (is_null($ticket->auxiliar_id)) {
                    $ticket->auxiliar_id = $usuario->id;
                }
                $ticket->save();
            }
        }

        // 4. Redireccionar de vuelta a la página del ticket
        return back()->with('success', 'Comentario añadido exitosamente.');
    }

    /**
     * Muestra solo los tickets creados por el usuario logueado (ruta /mis-tickets).
     */
    public function misTickets(): View
    {
        $tickets = Ticket::where('usuario_id', Auth::id())
                          ->with(['creador', 'auxiliar'])
                          ->latest()
                          ->get();

        // Puedes pasar un título adicional a la vista de índice para diferenciarla
        return view('tickets.index', ['tickets' => $tickets, 'headerTitle' => 'Mis Tickets Creados']);
    }
}
