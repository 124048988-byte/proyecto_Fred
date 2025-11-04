<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Listado de Tickets
        </h2>
        <a href="{{ route('tickets.create') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Crear Nuevo Ticket
        </a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif
            {{-- Manejo de mensajes de error o éxito --}}
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                
                                {{-- AÑADIDO: Título del Ticket --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                                
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Creador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Auxiliar Asignado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Creación</th>
                                {{-- ESPACIO PARA ACCIONES (Ver, Editar, etc.) --}}
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->id }}</td>
                                    
                                    {{-- CAMPO TÍTULO --}}
                                    <td class="px-6 py-4 font-bold text-sm text-gray-900 dark:text-gray-100">
                                        {{ $ticket->title }}
                                    </td>

                                    {{-- CAMPO DESCRIPCIÓN (Se añadió truncate para evitar que rompa la tabla) --}}
                                    <td class="px-6 py-4 max-w-xs overflow-hidden truncate text-sm text-gray-500 dark:text-gray-300" title="{{ $ticket->description ?? $ticket->descripcion }}">
                                        {{ Str::limit($ticket->description ?? $ticket->descripcion, 50) }}
                                    </td>
                                    
                                    {{-- CAMPO ESTADO con estilos --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($ticket->estatus === 'pendiente') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                            @elseif($ticket->estatus === 'en progreso') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                            @else bg-green-100 text-green-800 dark:bg-green-100 dark:text-green-800
                                            @endif">
                                            {{ ucfirst($ticket->estatus) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->creador->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->auxiliar ? $ticket->auxiliar->name : 'No Asignado' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                    
                                    {{-- Columna de Acciones: CORRECCIÓN DEL ENLACE "VER" --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-600">
                                            {{ __('Ver') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($tickets->isEmpty())
                        <p class="text-center py-4">No hay tickets para mostrar.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
