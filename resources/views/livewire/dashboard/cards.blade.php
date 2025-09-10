{{-- Wrapper mais alto (sem centralizar verticalmente) --}}
<div class="mx-auto w-full max-w-4xl px-4 md:px-6 pt-4 md:pt-6 lg:pt-8">
  <div class="grid grid-cols-1 gap-6">

    {{-- CARD: SALDO + ATALHOS --}}
    <div class="overflow-hidden rounded-2xl bg-white/80 backdrop-blur shadow-lg ring-1 ring-slate-200">
      <div class="p-6 md:p-8">
        {{-- Gradiente no padrão sky --}}
        <div class="rounded-2xl bg-gradient-to-r from-sky-600 to-sky-700 text-white px-6 py-6 md:px-8 md:py-7 shadow-sm">
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3 md:gap-4">
              <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white/15">
                {{-- ícone carteira --}}
                <svg class="h-6 w-6 md:h-7 md:w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-width="2" d="M3 7h13a3 3 0 013 3v4a3 3 0 01-3 3H3zM3 7V6a2 2 0 012-2h9" />
                  <path stroke-linecap="round" stroke-width="2" d="M16 10h4v4h-4z"/>
                </svg>
              </span>
              <div>
                <div class="text-sm md:text-base/6 opacity-90">Saldo atual</div>
                <div class="text-xs md:text-sm opacity-80">Disponível</div>
              </div>
            </div>
          </div>

          <div class="mt-4 text-4xl md:text-5xl font-semibold tracking-tight">
            R$ {{ number_format($balance, 2, ',', '.') }}
          </div>
        </div>

        {{-- Atalhos principais: 2x2 em md (notebook), 4 colunas em lg+ --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
          {{-- Usuários --}}
          <a href="{{ route('users.index') }}"
             class="group flex flex-col items-center gap-2 md:gap-3 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 px-4 py-4 md:px-5 md:py-5 transition
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-600/40">
            <span class="inline-flex h-12 w-12 md:h-14 md:w-14 items-center justify-center rounded-xl bg-sky-600 text-white transition group-hover:bg-sky-700 group-hover:scale-105">
              {{-- ícone usuários --}}
              <svg class="h-6 w-6 md:h-7 md:w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M12 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                <path stroke-linecap="round" stroke-width="2" d="M20 8v6M23 11h-6"/>
              </svg>
            </span>
            <span class="text-xs md:text-sm font-medium text-slate-900">Usuários</span>
          </a>

          {{-- Carteira / Extrato --}}
          <a href="{{ route('wallet.extrato') }}"
             class="group flex flex-col items-center gap-2 md:gap-3 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 px-4 py-4 md:px-5 md:py-5 transition
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-600/40">
            <span class="inline-flex h-12 w-12 md:h-14 md:w-14 items-center justify-center rounded-xl bg-sky-600 text-white transition group-hover:bg-sky-700 group-hover:scale-105">
              {{-- ícone carteira --}}
              <svg class="h-6 w-6 md:h-7 md:w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="2" d="M3 7h13a3 3 0 013 3v4a3 3 0 01-3 3H3zM3 7V6a2 2 0 012-2h9" />
              </svg>
            </span>
            <span class="text-xs md:text-sm font-medium text-slate-900">Carteira</span>
          </a>

          {{-- Transferir --}}
          <a href="{{ route('transfer.wizard') }}"
             class="group flex flex-col items-center gap-2 md:gap-3 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 px-4 py-4 md:px-5 md:py-5 transition
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-600/40">
            <span class="inline-flex h-12 w-12 md:h-14 md:w-14 items-center justify-center rounded-xl bg-sky-600 text-white transition group-hover:bg-sky-700 group-hover:scale-105">
              {{-- ícone transferência --}}
              <svg class="h-6 w-6 md:h-7 md:w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="2" d="M7 12h10m0 0-3-3m3 3-3 3"/>
              </svg>
            </span>
            <span class="text-xs md:text-sm font-medium text-slate-900">Transferir</span>
          </a>

          {{-- Notificações --}}
          <a href="{{ route('notification.index') }}"
             class="group flex flex-col items-center gap-2 md:gap-3 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 px-4 py-4 md:px-5 md:py-5 transition
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-600/40">
            <span class="inline-flex h-12 w-12 md:h-14 md:w-14 items-center justify-center rounded-xl bg-sky-600 text-white transition group-hover:bg-sky-700 group-hover:scale-105">
              {{-- ícone notificações --}}
              <svg class="h-6 w-6 md:h-7 md:w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5"/>
                <path stroke-linecap="round" stroke-width="2" d="M9 21a3 3 0 006 0"/>
              </svg>
            </span>
            <span class="text-xs md:text-sm font-medium text-slate-900">Notificações</span>
          </a>
        </div>
      </div>
    </div>

  </div>
</div>
