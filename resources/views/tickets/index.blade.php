<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            {{-- Título ajustado para coincidir con la imagen --}}
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-3 sm:mb-0">
                Listado de Tickets
            </h2>
            <div class="flex space-x-3">
                {{-- Enlace a Mis Tickets --}}
                <a href="{{ route('tickets.mine') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150">
                    Mis Tickets
                </a>
                
                {{-- Botón Crear Nuevo Ticket (Visible para todos) --}}
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Crear Nuevo Ticket
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Manejo de mensajes de éxito o error --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-x-auto">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Creador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Auxiliar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Creación</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->id }}</td>
                                    
                                    {{-- CAMPO TÍTULO con enlace a la vista de detalle --}}
                                    <td class="px-6 py-4 font-bold text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-600">
                                        <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->title }}</a>
                                    </td>
                                    
                                    {{-- CAMPO ESTADO con estilos --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            // Convertir a minúsculas para coincidencia consistente
                                            $lowerStatus = strtolower($ticket->estatus); 
                                            
                                            $badgeClasses = match($lowerStatus) { 
                                                'pendiente', 'abierto' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100', 
                                                'en progreso' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                                'cerrado' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                'cancelado' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100', 
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClasses }}">
                                            {{ ucfirst($ticket->estatus) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->creador->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->auxiliar ? $ticket->auxiliar->name : 'No Asignado' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                    
                                    {{-- Columna de Acciones CORREGIDA --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        
                                        {{-- 1. Botón VER (Disponible para todos) --}}
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-600">
                                            Ver
                                        </a>

                                        {{-- 2. ACCIONES DE AUXILIAR/ADMINISTRADOR (Rol 2) --}}
                                        @if (Auth::user()->rol_id == 2) 
                                            @if (strtolower($ticket->estatus) === 'pendiente' || strtolower($ticket->estatus) === 'abierto')
                                                {{-- Botón Tomar Ticket --}}
                                                <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-600 ml-2">
                                                        Tomar
                                                    </button>
                                                </form>
                                            @elseif (strtolower($ticket->estatus) === 'en progreso' && $ticket->auxiliar_id === Auth::id())
                                                {{-- Botón Cerrar Ticket (solo si el auxiliar asignado es el usuario actual) --}}
                                                <form action="{{ route('tickets.update_status', $ticket) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres CERRAR este ticket?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="estatus" value="cerrado">
                                                    <button type="submit" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-600 ml-2">
                                                        Cerrar
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        {{-- 3. ACCIONES DE CREADOR DE TICKET O ADMINISTRADOR (Rol 1) --}}
                                        {{-- Los botones EDITAR y CANCELAR fueron ELIMINADOS de esta vista,
                                             aunque la lógica original los permitía bajo ciertas condiciones. 
                                             Ahora solo se mostrará "Ver" y las acciones de Auxiliar/Admin. --}}
                                        {{-- El condicional @if (Auth::user()->rol_id == 1 || Auth::id() === $ticket->usuario_id) se mantiene,
                                             pero su contenido (Editar y Cancelar) se ha quitado, 
                                             ya que tu solicitud era no mostrarlos en esta vista. --}}

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                        No hay tickets para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Enlace de paginación --}}
                    <div class="mt-6">
                        {{ $tickets->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>