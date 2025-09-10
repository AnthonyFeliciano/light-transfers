<div x-data="{ step: @entangle('step') }"
     class="max-w-3xl mx-auto">

  {{-- topo: voltar + título + subtítulo --}}
  <div class="mb-4 flex items-center gap-3 text-sm">
    <button type="button" onclick="history.back()"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-width="2" d="M15 18l-6-6 6-6"/>
      </svg>
      Voltar
    </button>
  </div>

  <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Nova Transferência</h1>
  <p class="text-sm text-slate-500 mt-1">Passo <span x-text="step"></span> de 3</p>

  {{-- STEPPER --}}
  <div class="mt-4 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 shadow-sm p-6">
    <div class="h-1.5 w-full bg-slate-200/70 rounded-full overflow-hidden">
      <!-- cor do preenchimento alterada para azul padrão -->
      <div class="h-full bg-sky-600 rounded-full transition-all"
           :style="{ width: (step === 1 ? 8 : step === 2 ? 58 : 100) + '%' }"></div>
    </div>

    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
      <div>
        <div class="mx-auto h-9 w-9 rounded-full grid place-items-center text-sm font-medium transition"
             :class="step >= 1 ? 'bg-sky-600 text-white' : 'bg-slate-200 text-slate-600'">1</div>
        <div class="mt-2 text-sm font-medium"
             :class="step >= 1 ? 'text-slate-900' : 'text-slate-500'">Destinatário</div>
        <div class="text-xs text-slate-500">Escolha quem vai receber</div>
      </div>
      <div>
        <div class="mx-auto h-9 w-9 rounded-full grid place-items-center text-sm font-medium"
             :class="step >= 2 ? 'bg-sky-600 text-white' : 'bg-slate-200 text-slate-600'">2</div>
        <div class="mt-2 text-sm font-medium"
             :class="step >= 2 ? 'text-slate-900' : 'text-slate-500'">Valor</div>
        <div class="text-xs text-slate-500">Defina o valor</div>
      </div>
      <div>
        <div class="mx-auto h-9 w-9 rounded-full grid place-items-center text-sm font-medium"
             :class="step >= 3 ? 'bg-sky-600 text-white' : 'bg-slate-200 text-slate-600'">3</div>
        <div class="mt-2 text-sm font-medium"
             :class="step >= 3 ? 'text-slate-900' : 'text-slate-500'">Confirmar</div>
        <div class="text-xs text-slate-500">Revise os dados</div>
      </div>
    </div>
  </div>

  {{-- PASSO 1: Destinatário --}}
  <div x-show="step===1" x-cloak class="mt-6 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 shadow-sm p-6">
    <div class="mb-4">
      <div class="text-base font-semibold text-slate-900">Escolha o Destinatário</div>
      <div class="text-sm text-slate-500">Digite o CPF ou e-mail de quem vai receber a transferência</div>
    </div>

    <div class="flex items-center gap-2">
      <div class="relative flex-1">
        <input type="text" wire:model.defer="identifier"
               class="w-full rounded-xl border-slate-200 px-3 py-2 shadow-sm focus:outline-none
                      focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600"
               placeholder="123.456.789-01 ou usuario@email.com">
        @error('identifier') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <!-- BOTÃO PRIMÁRIO AZUL -->
      <button type="button" wire:click="findPayee" wire:loading.attr="disabled" wire:target="findPayee"
              class="inline-flex items-center gap-2 rounded-xl bg-sky-600 text-white px-4 py-2.5
                     hover:bg-sky-700 disabled:opacity-60">
        <span>Buscar</span>
      </button>
    </div>

    <div class="mt-6 flex justify-end">
      <!-- BOTÃO PRIMÁRIO AZUL -->
      <button type="button" wire:click="findPayee" wire:loading.attr="disabled" wire:target="findPayee"
              class="inline-flex items-center gap-2 rounded-xl bg-sky-600 text-white px-5 py-2.5
                     hover:bg-sky-700 disabled:opacity-60">
        <span>Continuar</span>
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  {{-- PASSO 2: Valor --}}
  <div x-show="step===2" x-cloak class="mt-6 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 shadow-sm p-6">
    <div class="mb-4">
      <div class="text-base font-semibold text-slate-900">
        Valor da transferência
        <span class="font-normal text-slate-500">para
          <span class="font-medium text-slate-900">{{ $this->payee?->name }}</span>
        </span>
      </div>
    </div>

    <div class="rounded-xl bg-sky-50 ring-1 ring-sky-200 px-4 py-3 mb-4">
      <div class="text-sm text-slate-600">Saldo disponível</div>
      <div class="text-xl font-semibold text-slate-900">
        R$ {{ number_format((float)$available,2,',','.') }}
      </div>
    </div>

    <label class="block text-sm font-medium text-slate-700 mb-1">Valor</label>
    <div class="relative">
      <span class="absolute inset-y-0 left-3 my-auto text-slate-500 text-sm">R$</span>
      <input type="number" step="0.01" min="0.01" wire:model.defer="amount"
             class="w-full rounded-xl border-slate-200 pl-9 pr-3 py-2 shadow-sm focus:outline-none
                    focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600"
             placeholder="0,00">
    </div>
    @error('amount') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

    <div class="mt-4 flex flex-wrap gap-3">
      @foreach([10,50,100,200] as $q)
        <button type="button"
                class="rounded-xl px-4 py-2 text-slate-700 bg-slate-100 hover:bg-slate-200"
                wire:click="$set('amount','{{ number_format($q,2,'.','') }}')">
          R$ {{ number_format($q,0,',','.') }}
        </button>
      @endforeach
    </div>

    <div class="mt-6 flex items-center justify-between">
      <button type="button" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 bg-slate-100 hover:bg-slate-200"
              x-on:click="step=1">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
        Voltar
      </button>
      <!-- BOTÃO PRIMÁRIO AZUL -->
      <button type="button" wire:click="validateAmount" wire:loading.attr="disabled" wire:target="validateAmount"
              class="inline-flex items-center gap-2 rounded-xl bg-sky-600 text-white px-5 py-2.5 hover:bg-sky-700">
        <span>Continuar</span>
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  {{-- PASSO 3: Revisão --}}
  <div x-show="step===3" x-cloak class="mt-6 space-y-6">
    <div class="rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 shadow-sm p-6">
      <div class="text-base font-semibold text-slate-900 mb-4">Revisar Transferência</div>

      {{-- participantes --}}
      <div class="rounded-xl ring-1 ring-slate-200 p-4 bg-white/60">
        <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
          <div class="flex items-center gap-3">
            <span class="h-9 w-9 rounded-full bg-slate-100 grid place-items-center text-slate-700">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5.5 21a6.5 6.5 0 0113 0M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
            </span>
            <div>
              <div class="font-medium text-slate-900">{{ auth()->user()->name }}</div>
              <div class="text-xs text-slate-500">Pagador • {{ auth()->user()->email }}</div>
            </div>
          </div>

          <div class="hidden md:grid place-items-center">
            <span class="h-9 w-9 rounded-full bg-slate-100 grid place-items-center text-slate-700">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
          </div>

          <div class="flex items-center gap-3">
            <span class="h-9 w-9 rounded-full bg-emerald-50 text-emerald-700 grid place-items-center">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5.5 21a6.5 6.5 0 0113 0M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
            </span>
            <div>
              <div class="font-medium text-slate-900">{{ $this->payee?->name }}</div>
              <div class="text-xs text-slate-500">Recebedor • {{ $this->payee?->email }}</div>
            </div>
            @if($this->payee?->role)
              <span class="ml-2 text-[11px] rounded-full px-2 py-0.5 bg-sky-600 text-white">{{ strtoupper($this->payee->role) }}</span>
            @endif
          </div>
        </div>
      </div>

      {{-- valores --}}
      <div class="mt-4 rounded-xl ring-1 ring-slate-200 p-4 bg-white/60">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div class="flex items-center justify-between">
            <dt class="text-slate-600">Valor</dt>
            <dd class="font-semibold text-slate-900">R$ {{ number_format((float)$amount,2,',','.') }}</dd>
          </div>
          <div class="flex items-center justify-between">
            <dt class="text-slate-600">Taxa</dt>
            <dd class="text-slate-900">R$ 0,00</dd>
          </div>
          <div class="md:col-span-2 flex items-center justify-between pt-2 border-t">
            <dt class="text-slate-900 font-medium">Total</dt>
            <dd class="text-slate-900 font-semibold">R$ {{ number_format((float)$amount,2,',','.') }}</dd>
          </div>
        </dl>
        <div class="mt-2 text-xs text-slate-500">{{ now()->format('d/m/Y, H:i') }}</div>
      </div>

      {{-- aviso + ações --}}
      <div class="mt-4 rounded-xl bg-slate-50 ring-1 ring-slate-200 p-4 text-sm flex items-center justify-between">
        <div class="flex items-center gap-2 text-slate-700">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
          </svg>
          <span>A autorização será verificada automaticamente ao confirmar.</span>
        </div>
        @if($authMsg)
          <span class="text-rose-700">{{ $authMsg }}</span>
        @endif
      </div>

      <div class="mt-6 flex items-center justify-between">
        <button type="button"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 bg-slate-100 hover:bg-slate-200"
                x-on:click="step=2">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M15 18l-6-6 6-6"/>
          </svg>
          Voltar
        </button>

        <!-- BOTÃO PRIMÁRIO AZUL -->
        <button type="button"
                wire:click="confirm"
                wire:loading.attr="disabled"
                wire:target="confirm"
                class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-white transition bg-sky-600 hover:bg-sky-700 disabled:opacity-60">
          <span wire:loading.remove wire:target="confirm">Confirmar Transferência</span>
          <span wire:loading wire:target="confirm">Autorizando…</span>
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

</div>
