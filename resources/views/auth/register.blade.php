<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4 pb-2 border-b border-gray-200">
            <p class="text-sm font-semibold text-gray-700">Dados da Loja</p>
            <p class="text-xs text-gray-500">Crie a conta da sua loja no AteliêPro.</p>
        </div>

        <!-- Nome Fantasia da Loja -->
        <div>
            <x-input-label for="nome_fantasia" :value="__('Nome da Loja')" />
            <x-text-input id="nome_fantasia" class="block mt-1 w-full" type="text" name="nome_fantasia" :value="old('nome_fantasia')" required autofocus autocomplete="organization" placeholder="Ex: Bella Noivas" />
            <x-input-error :messages="$errors->get('nome_fantasia')" class="mt-2" />
        </div>

        <!-- CNPJ / CPF -->
        <div class="mt-4">
            <x-input-label for="cnpj_cpf" :value="__('CNPJ ou CPF')" />
            <x-text-input id="cnpj_cpf" class="block mt-1 w-full" type="text" name="cnpj_cpf" :value="old('cnpj_cpf')" required autocomplete="off" />
            <x-input-error :messages="$errors->get('cnpj_cpf')" class="mt-2" />
        </div>

        <!-- Telefone (opcional) -->
        <div class="mt-4">
            <x-input-label for="telefone" :value="__('Telefone / WhatsApp (opcional)')" />
            <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
        </div>

        <div class="mt-6 mb-4 pb-2 border-b border-gray-200">
            <p class="text-sm font-semibold text-gray-700">Seu Acesso (Administrador)</p>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Seu Nome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
