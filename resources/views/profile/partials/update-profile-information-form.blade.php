<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Información del Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Actualiza la información de la cuenta, foto y correo electrónico de tu perfil.") }}
        </p>
    </header>

    {{-- CRUCIAL: Añadir enctype para permitir la subida de archivos (fotos) --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- DISPLAY Y CAMPO PARA LA FOTO DE PERFIL --}}
        <div>
            <x-input-label for="foto_perfil" :value="__('Cambiar Foto de Perfil')" />
            
            <div class="flex items-center space-x-500 mt-50">
                {{-- Muestra la foto actual o un placeholder --}}
                <div class="flex-shrink-0">
                    @php
                        // CORRECCIÓN: Usamos la Facade Storage en la vista para construir la URL.
                        // También nos aseguramos de que el placeholder use un estilo más consistente.
                        $photoUrl = $user->foto_perfil 
                                    ? \Illuminate\Support\Facades\Storage::url($user->foto_perfil)
                                    : 'https://placehold.co/80x80/36454F/white?text=' . substr($user->name, 0, 1);
                    @endphp
                    {{-- Cambiado a img src con ruta corregida --}}
                    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover border border-gray-700">
                </div>
                
                {{-- Input para subir la nueva foto --}}
                <input id="foto_perfil" name="foto_perfil" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('foto_perfil')" />
        </div>
        
        {{-- CAMPO NOMBRE (EDITABLE) --}}
        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- CAMPO EMAIL (EDITABLE) --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Tu dirección de correo no está verificada.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Haz click aquí para reenviar el email de verificación.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Un nuevo enlace de verificación ha sido enviado a tu dirección de correo.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- CAMPOS DE SÓLO LECTURA (Departamento y Puesto) --}}
        <div>
            <x-input-label for="departamento" :value="__('Departamento')" />
            <x-text-input id="departamento" 
                          type="text" 
                          class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" 
                          :value="$user->departamento->nombre ?? 'N/A'" 
                          disabled />
        </div>

        <div>
            <x-input-label for="rol" :value="__('Puesto/Rol')" />
            <x-text-input id="rol" 
                          type="text" 
                          class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" 
                          :value="$user->rol->nombre ?? 'N/A'" 
                          disabled />
        </div>

        {{-- BOTÓN GUARDAR Y MENSAJES DE ESTADO --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
    
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
</section>
