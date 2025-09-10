<!doctype html>
<html lang="pt-BR" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'Light Transfers' }}</title>

  <style>[x-cloak]{display:none!important}</style>
  @vite(['resources/css/app.css','resources/js/app.js'])
  @livewireStyles
</head>
<body class="min-h-dvh bg-gradient-to-b from-slate-50 via-slate-50 to-slate-100 text-slate-800 antialiased">

  @auth
  {{-- HEADER (navbar só para autenticados) --}}
  <header class="sticky top-0 z-40 bg-white/75 backdrop-blur shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      {{-- brand --}}
      <a href="{{ url('/') }}" class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-white font-bold
                     bg-gradient-to-br from-sky-500 to-indigo-600 shadow-sm">LT</span>
        <span class="font-semibold tracking-tight text-slate-900">Light Transfers</span>
      </a>

      <div x-data="{open:false, usermenu:false}" class="flex items-center gap-3">
        {{-- NAV DESKTOP --}}
        <nav class="hidden md:flex items-center gap-1 text-sm">
          @php
            $link = fn($route,$label,$match)=>"<a href='".route($route)."' class='px-3 py-2 rounded-lg transition ".
              (request()->routeIs($match) ? "bg-slate-200/70 text-slate-900 shadow-sm" : "text-slate-600 hover:text-slate-900 hover:bg-slate-100")."'>$label</a>";
          @endphp
          {!! $link('dashboard','Dashboard','dashboard') !!}
          {!! $link('transfer.wizard','Transferir','transfer.wizard') !!}
          {!! $link('wallet.extrato','Carteira','wallet.*') !!}
          {!! $link('notification.index','Notificações','outbox.*') !!}
          {!! $link('users.index','Usuários','users.*') !!}
        </nav>

        {{-- USER DROPDOWN --}}
        <div class="relative">
          <button @click="usermenu = !usermenu" class="hidden md:inline-flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full
                         bg-gradient-to-br from-indigo-500 to-violet-600 text-white text-xs shadow-sm">
              {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
            </span>
            <span class="text-sm font-medium max-w-[10rem] truncate text-slate-800">
              {{ auth()->user()->name ?? 'Usuário' }}
            </span>
            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/></svg>
          </button>
          <div x-cloak x-show="usermenu" @click.outside="usermenu=false"
               class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg/50 shadow-lg overflow-hidden">
            <div class="px-3 py-2 text-xs text-slate-500">Sessão</div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50">Sair</button>
            </form>
          </div>
        </div>

        {{-- MOBILE TOGGLE --}}
        <button @click="open = !open"
                class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-lg hover:bg-slate-100"
                aria-label="Abrir menu">
          <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          <svg x-show="open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- MOBILE MENU --}}
        <div x-cloak x-show="open" @click.outside="open=false" class="absolute inset-x-0 top-16 md:hidden bg-white shadow-md">
          <nav class="px-4 py-3 grid gap-1">
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('dashboard') ? 'bg-slate-200/70 text-slate-900' : 'text-slate-700' }}">Dashboard</a>
            <a href="{{ route('transfer.wizard') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('transfer.wizard') ? 'bg-slate-200/70 text-slate-900' : 'text-slate-700' }}">Transferir</a>
            <a href="{{ route('wallet.extrato') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('wallet.*') ? 'bg-slate-200/70 text-slate-900' : 'text-slate-700' }}">Carteira</a>
            <a href="{{ route('notification.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('outbox.*') ? 'bg-slate-200/70 text-slate-900' : 'text-slate-700' }}">Notificações</a>
            <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('users.*') ? 'bg-slate-200/70 text-slate-900' : 'text-slate-700' }}">Usuários</a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
              @csrf
              <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700">Sair</button>
            </form>
          </nav>
        </div>
      </div>
      @endauth

      {{-- @guest: nada de navbar/CTAs --}}
    </div>
  </header>

  {{-- MAIN --}}
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
  </main>

  {{-- LOADING GLOBAL (sutil) --}}
  <div wire:loading.delay.short
       class="fixed inset-x-0 top-0 z-[60] h-1 bg-gradient-to-r from-indigo-500 via-sky-500 to-blue-500 animate-pulse"></div>

  {{-- TOASTS + LISTENERS GLOBAIS --}}
  <div x-data="{items:[]}"
       x-on:toast.window="items.push($event.detail); setTimeout(()=>items.shift(), 3500)"
       x-on:wallet-updated.window="items.push({type:'success', msg:'Saldo atualizado.'}); setTimeout(()=>items.shift(), 3500)"
       x-on:notify.window="items.push($event.detail); setTimeout(()=>items.shift(), 3500)"
       class="fixed right-4 bottom-4 space-y-2 z-50">
    <template x-for="(t, i) in items" :key="i">
      <div class="px-4 py-2 rounded-xl shadow-lg bg-white/95 backdrop-blur-sm"
           :class="{
             'bg-emerald-50 text-emerald-800': t.type==='success',
             'bg-rose-50 text-rose-800'      : t.type==='error',
             'bg-amber-50 text-amber-900'    : t.type==='warn'
           }"
           x-text="t.msg"></div>
    </template>
  </div>

  <div
    x-data
    x-init="
      const s = $el.dataset.success;
      const e = $el.dataset.error;
      const v = $el.dataset.validation;
      if (s) window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', msg: s } }));
      if (e) window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error',   msg: e } }));
      if (v) window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error',   msg: v } }));
    "
    data-success="{{ session('success') }}"
    data-error="{{ session('error') }}"
    data-validation="{{ $errors->first() }}"
  ></div>


  @livewireScripts
</body>
</html>
