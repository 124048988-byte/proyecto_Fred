<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Nuevo Ticket') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Formulario para crear un nuevo ticket --}}
                    <form method="POST" action="{{ route('tickets.store') }}">
                        @csrf
                        
                        {{-- CAMPO TÍTULO --}}
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Título del Ticket')" />
                            <x-text-input 
                                id="title" 
                                name="title" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('title')" 
                                placeholder="Ej: No puedo iniciar sesión en la VPN"
                                required 
                                autofocus 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        {{-- CAMPO DESCRIPCIÓN --}}
                        <div class="mb-6">
                            <x-input-label for="descripcion" :value="__('Descripción Detallada del Problema')" />
                            <textarea 
                                id="descripcion" 
                                name="descripcion" 
                                rows="6" 
                                class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                required
                                placeholder="Describe el problema, cuándo comenzó y qué pasos has intentado para resolverlo."
                            >{{ old('descripcion') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            {{-- Botón de Cancelar --}}
                            <a href="{{ route('tickets.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                {{ __('Cancelar') }}
                            </a>
                            
                            {{-- Botón de Enviar --}}
                            <x-primary-button>
                                {{ __('Enviar Ticket') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
