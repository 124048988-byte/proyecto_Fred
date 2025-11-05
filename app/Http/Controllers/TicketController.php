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
     * Muestra la lista de tickets.
     * Si el usuario es Jefe (rol_id=1), muestra TODOS los tickets.
     * Si el usuario NO es Jefe, solo muestra sus tickets creados.
     */
    public function index(): View
    {
        $usuario = Auth::user();

        // 1. Obtener el rol del usuario
        $isJefe = $usuario->rol_id === 1;

        // 2. Construir la consulta base
        // Usamos latest() que es un alias de orderBy('created_at', 'desc')
        $query = Ticket::with(['creador', 'auxiliar'])->latest();

        // 3. Aplicar filtro si NO es Jefe
        if (!$isJefe) {
            $query->where('usuario_id', Auth::id());
        }

        // 4. Ejecutar la consulta y OBTENER UN PAGINATOR usando paginate()
        $tickets = $query->paginate(10); // Pagina 10 tickets por página

        // 5. Definir el título de la cabecera
        $headerTitle = $isJefe ? 'Listado de Todos los Tickets' : 'Mis Tickets Creados';
        
        return view('tickets.index', ['tickets' => $tickets, 'headerTitle' => $headerTitle]);
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
     * @param  \App\Models\Ticket  $ticket
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
     * ***************************************************************
     * NOTA: Este método se deja por si la ruta DELETE (Resource) existe.
     * Se mantiene la lógica de autorización anterior.
     * ***************************************************************
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        // 1. Verificar si el usuario autenticado es el creador
        if (Auth::id() !== $ticket->usuario_id) {
            return redirect()->route('tickets.index')
                             ->with('error', 'No tienes permiso para eliminar este ticket.');
        }

        // 2. Si el estatus NO es 'pendiente' O si tiene auxiliar asignado, deniega la eliminación.
        if ($ticket->estatus !== 'pendiente' || !is_null($ticket->auxiliar_id)) { 
            return redirect()->route('tickets.show', $ticket)
                             ->with('error', 'Solo se pueden eliminar tickets pendientes y sin asignar.');
        }

        try {
            // Eliminación física del registro
            $ticket->delete();
            return redirect()->route('tickets.index')
                             ->with('success', 'Ticket eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar ticket: ' . $e->getMessage()); 
            return redirect()->back()->with('error', 'Ocurrió un error al intentar eliminar el ticket.');
        }
    }

    // =========================================================================
    // MÉTODOS DE ACCIÓN PERSONALIZADA DE ESTADO
    // =========================================================================

    /**
     * Cancela el ticket. Solo lo puede hacer el creador si no está cerrado.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Ticket $ticket): RedirectResponse
    {
        // 1. Autorización: Solo el creador puede cancelar.
        if (Auth::id() !== $ticket->usuario_id) {
            return redirect()->route('tickets.show', $ticket)
                             ->with('error', 'Solo puedes cancelar tus propios tickets.');
        }

        // 2. Validación de Estado: No se puede cancelar si ya está 'cerrado' o 'cancelado'.
        if ($ticket->estatus === 'cerrado' || $ticket->estatus === 'cancelado') {
             return redirect()->route('tickets.show', $ticket)
                              ->with('error', 'El ticket ya ha sido finalizado y no puede ser cancelado.');
        }
        
        // 3. Actualización de Estado a 'cancelado'
        $ticket->estatus = 'cancelado';
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)
                          ->with('success', 'Ticket #' . $ticket->id . ' cancelado exitosamente. Estatus: Cancelado.');
    }

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
        
        // 2. Validación de Estado: No se puede cerrar si ya está cerrado o cancelado.
        if ($ticket->estatus === 'cerrado' || $ticket->estatus === 'cancelado') {
             return redirect()->route('tickets.show', $ticket)
                              ->with('error', 'El ticket ya estaba marcado como Terminado o fue cancelado.');
        }

        // 3. Actualización de Estado
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

        // 3. Lógica de Asignación Automática (MEJORADA)
        $usuario = Auth::user();
        $authUserId = Auth::id();
        
        // A) El usuario que comenta es AUXILIAR o JEFE (rol_id 1 o 2)
        $isSupportUser = ($usuario->rol_id === 1 || $usuario->rol_id === 2);
        
        // Bandera para saber si se asignó o no el ticket
        $ticketWasAssigned = false; 

        if ($isSupportUser && $ticket->estatus === 'pendiente') {
            
            // Si está pendiente y es soporte, revisamos si tiene auxiliar.
            if (is_null($ticket->auxiliar_id)) {
                 // ASIGNACIÓN AUTOMÁTICA
                $ticket->estatus = 'en progreso';
                $ticket->auxiliar_id = $authUserId;
                $ticket->save();
                $ticketWasAssigned = true; 
            } else {
                // Si ya estaba asignado a otro auxiliar, y solo está pendiente de estatus
                // Solo cambiamos el estatus a 'en progreso' si el auxiliar asignado es el que comenta.
                if ($ticket->auxiliar_id === $authUserId) {
                    $ticket->estatus = 'en progreso';
                    $ticket->save();
                }
            }
        }
        
        // 4. Redireccionar con el mensaje adecuado (DIFERENCIADO)
        if ($ticketWasAssigned) {
            // Mensaje de éxito específico si se tomó el ticket
            return back()->with('success', 'Comentario añadido. ¡Ticket #' . $ticket->id . ' tomado exitosamente y en progreso!');
        }
        
        // Si el ticket ya estaba asignado, o lo comentó el creador (rol_id 3), o solo se cambió el estado
        return back()->with('success', 'Comentario añadido exitosamente. No hay cambios en la asignación.');
    }

    /**
     * Este método ya no tiene sentido si 'index' maneja la lógica de filtrado por rol.
     * Lo redirigiremos a index y mantendremos la ruta para evitar errores 404
     */
    public function misTickets(): RedirectResponse
    {
        // Redirige al index, que ahora aplica el filtro automático si no es Jefe.
        return redirect()->route('tickets.index');
    }
}
