<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Ticket #{{ $ticket->id }} - <span class="text-indigo-600 dark:text-indigo-400">{{ $ticket->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensajes de Estado y Error --}}
            @if (session('status'))
                <div class="bg-green-100 dark:bg-green-800 border border-green-400 text-green-700 dark:text-green-100 px-4 py-3 rounded-xl relative shadow-md" role="alert">
                    <span class="block sm:inline font-medium">{{ session('status') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 dark:bg-red-800 border border-red-400 text-red-700 dark:text-red-100 px-4 py-3 rounded-xl relative shadow-md" role="alert">
                    <span class="block sm:inline font-medium">{{ session('error') }}</span>
                </div>
            @endif
            @if (session('success'))
                {{-- Usa 'success' si lo envías desde el controlador (ej. al añadir comentario) --}}
                <div class="bg-blue-100 dark:bg-blue-800 border border-blue-400 text-blue-700 dark:text-blue-100 px-4 py-3 rounded-xl relative shadow-md" role="alert">
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tarjeta de Información Principal del Ticket -->
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-xl">
                <div class="p-6">
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">Detalles del Reporte</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 text-sm">
                        <!-- CREADOR -->
                        <div class="flex flex-col">
                            <p class="font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Creado por:</p>
                            <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $ticket->creador->name }}</p>
                        </div>

                        <!-- AUXILIAR ASIGNADO -->
                        <div class="flex flex-col">
                            <p class="font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Asignado a:</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $ticket->auxiliar ? $ticket->auxiliar->name : 'Ningún auxiliar asignado.' }}</p>
                        </div>
                        
                        <!-- DEPARTAMENTO -->
                        <div class="flex flex-col">
                            <p class="font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Departamento:</p>
                            {{-- **Nota:** Asumo que el modelo Ticket tiene una relación 'departamento' --}}
                            <p class="text-gray-900 dark:text-gray-100">{{ $ticket->departamento ? $ticket->departamento->nombre : 'N/A' }}</p>
                        </div>

                        <!-- FECHA DE CREACIÓN -->
                        <div class="flex flex-col">
                            <p class="font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha de Creación:</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <!-- ESTATUS -->
                        <div class="flex flex-col col-span-1 md:col-span-2">
                            <p class="font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Estatus:</p>
                            {{-- Aplicamos estilos condicionales basados en el estatus --}}
                            <span class="w-fit px-4 py-1 inline-flex text-base leading-5 font-bold rounded-full shadow-lg
                                @if($ticket->estatus === 'pendiente') bg-yellow-400 text-yellow-900 
                                @elseif($ticket->estatus === 'en progreso') bg-blue-500 text-white 
                                @else bg-green-500 text-white 
                                @endif">
                                {{ ucfirst($ticket->estatus) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque de Descripción -->
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-xl">
                <div class="p-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Descripción del Problema</h4>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-inner">
                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $ticket->descripcion }}</p>
                    </div>
                </div>
            </div>

            <!-- Bloque de Comentarios/Historial -->
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-xl">
                <div class="p-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Historial de Comentarios</h4>

                    {{-- Usar @forelse para evitar el error de 'foreach' si la colección está vacía --}}
                    @forelse ($ticket->comentarios as $comentario)
                        {{-- Distingue visualmente tu propio comentario del de otros usuarios --}}
                        <div class="mb-4 p-4 rounded-xl {{ $comentario->user_id === Auth::id() ? 'bg-indigo-50 dark:bg-indigo-900/30' : 'bg-gray-100 dark:bg-gray-700' }} shadow-md border-l-4 border-indigo-400 dark:border-indigo-600">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $comentario->user->name }} 
                                    @if ($comentario->user_id === Auth::id())
                                        <span class="text-xs font-normal text-indigo-600 dark:text-indigo-400 ml-1">(Tú)</span>
                                    @endif
                                </p>
                                {{-- Muestra el tiempo de forma relativa, por ejemplo: "hace 5 minutos" --}}
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $comentario->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $comentario->contenido }}</p>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-gray-400 text-center py-4">Aún no hay comentarios en este ticket.</p>
                    @endforelse
                </div>
            </div>

            <!-- Bloque de Añadir Comentario -->
            @if ($ticket->estatus !== 'cerrado')
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-xl">
                <div class="p-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Añadir Comentario</h4>
                    {{-- Ruta: tickets.add_comment --}}
                    <form method="POST" action="{{ route('tickets.add_comment', $ticket) }}" class="space-y-4">
                        @csrf

                        <div class="mt-1">
                            <label for="contenido" class="sr-only">Escribe tu comentario</label>
                            <textarea id="contenido" name="contenido" rows="4" 
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:text-sm" 
                                placeholder="Escribe tu comentario o actualización sobre el ticket aquí..."
                                required>{{ old('contenido') }}</textarea>
                        </div>
                        @error('contenido')
                            <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-400 focus:bg-indigo-700 dark:focus:bg-indigo-400 active:bg-indigo-900 dark:active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.693C5.125 15.423 6 13.73 6 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                Enviar Comentario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Bloque de ACCIONES -->
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-xl">
                <div class="p-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Acciones del Ticket</h4>
                    <div class="flex flex-wrap gap-4">
                        
                        <!-- BOTÓN VOLVER -->
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Volver al Listado
                        </a>

                        {{-- 1. BOTÓN TOMAR TICKET (Solo visible si el estatus es 'pendiente', NO está asignado, el usuario es soporte (rol_id=1 o 2) Y el usuario NO es el creador del ticket) --}}
                        @if (
                            $ticket->estatus === 'pendiente' && 
                            is_null($ticket->auxiliar_id) && 
                            (Auth::user()->rol_id === 1 || Auth::user()->rol_id === 2) &&
                            Auth::id() !== $ticket->usuario_id
                            )
                            <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-400 border border-transparent rounded-md font-semibold text-xs text-yellow-900 uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Tomar Ticket (Asignarme)
                                </button>
                            </form>
                        @endif

                        {{-- 2. BOTÓN MARCAR COMO TERMINADO (Solo si está asignado a ti O eres Jefe (rol_id=1) Y está 'en progreso') --}}
                        @if ($ticket->estatus === 'en progreso' && (Auth::id() === $ticket->auxiliar_id || Auth::user()->rol_id === 1))
                            <form method="POST" action="{{ route('tickets.update_status', $ticket) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estatus" value="cerrado">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-900 dark:active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Marcar como Terminado
                                </button>
                            </form>
                        @endif

                        {{-- 3. BOTÓN DE ELIMINACIÓN CONDICIONAL (Solo el creador si está 'pendiente' y NO asignado) --}}
                        @if (Auth::id() === $ticket->usuario_id && $ticket->estatus === 'pendiente' && is_null($ticket->auxiliar_id))
                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('¿Estás seguro de que quieres ELIMINAR el ticket #{{ $ticket->id }}? Esta acción es irreversible. (Usarás un pop-up simple, pero lo ideal es una modal)');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar Ticket
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
