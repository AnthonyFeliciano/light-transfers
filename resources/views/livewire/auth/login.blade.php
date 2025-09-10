@extends('components.layouts.app')

@php($title = 'Entrar')

@section('content')
<div class="grid min-h-[70dvh] place-items-center">
  <div class="w-full max-w-md">
    <div class="bg-white/80 backdrop-blur rounded-2xl shadow-sm ring-1 ring-slate-200 p-6">
      <div class="mb-6 text-center">
        <div class="mx-auto h-12 w-12 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white grid place-items-center shadow-sm">TL</div>
        <h1 class="mt-4 text-2xl font-semibold text-slate-900">Bem-vindo</h1>
        <p class="text-sm text-slate-500">Acesse sua conta para continuar</p>
      </div>

      @if ($errors->any())
        <div class="mb-4 rounded-xl bg-rose-50 text-rose-800 px-4 py-3">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.authenticate') }}" class="space-y-4">
        @csrf

        <div>
          <label for="email" class="block text-sm font-medium text-slate-700 mb-1">E-mail</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus
                 class="w-full rounded-xl border-slate-200 px-3 py-2 bg-white shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500
                        @error('email') ring-2 ring-rose-300 border-rose-300 @enderror">
          @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div x-data="{show:false}">
          <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
          <div class="relative">
            <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                   class="w-full rounded-xl border-slate-200 px-3 py-2 bg-white shadow-sm pr-10
                          focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500
                          @error('password') ring-2 ring-rose-300 border-rose-300 @enderror">
            <button type="button" @click="show=!show"
                    class="absolute inset-y-0 right-2 my-auto h-8 w-8 grid place-items-center rounded-lg text-slate-500 hover:bg-slate-100"
                    aria-label="Mostrar senha">
              <svg x-show="!show" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-width="2" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3" stroke-width="2"/>
              </svg>
              <svg x-show="show" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-width="2" d="M3 3l18 18M10.6 10.65A3 3 0 0113.35 13.4M6.4 6.5C3.9 8 2 12 2 12s3.5 7 10 7c2 0 3.7-.5 5.2-1.3M17.6 6.4A10.6 10.6 0 0012 5c-6.5 0-10 7-10 7a18.8 18.8 0 003.2 4.2"/>
              </svg>
            </button>
          </div>
          @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500/50">
            Lembrar
          </label>
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-slate-500 hover:text-slate-800">Esqueci a senha</a>
          @endif
        </div>

        <button class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-white font-medium
                       bg-gradient-to-r from-sky-500 to-indigo-600 shadow-sm hover:opacity-95 transition">
          Entrar
        </button>
      </form>

      <div class="mt-4 grid gap-2">
        {{-- Modo demo --}}
        <form method="POST" action="{{ route('login.demo') }}">
          @csrf
          <button class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2.5 font-medium
                         text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
            Entrar no modo demo
          </button>
        </form>

        {{-- Criar conta --}}
        @if (Route::has('register'))
          <a href="{{ route('register') }}"
             class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2.5 font-medium
                    text-white bg-slate-900/90 hover:bg-slate-900 transition">
            Criar nova conta
          </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
