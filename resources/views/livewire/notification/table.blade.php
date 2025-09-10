<div class="rounded-2xl bg-white/80 backdrop-blur p-6 shadow-sm ring-1 ring-slate-200">

  @php
    // Mapeamento: valor (DB) -> rótulo (PT-BR)
    $statusOptions = [
      ''         => 'Todos os status',
      'PENDING'  => 'Pendente',
      'SENT'     => 'Enviado',
      'FAILED'   => 'Falhou',
    ];

    // Cores dos badges por status (valor do DB)
    $badgeMap = [
      'SENT'    => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
      'PENDING' => 'bg-amber-50 text-amber-800 ring-amber-200',
      'FAILED'  => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
  @endphp

  {{-- Cabeçalho + Filtros --}}
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <div class="text-base font-semibold text-slate-900">Notificações</div>
      <div class="text-sm text-slate-500">Envios de notificação do sistema</div>
    </div>

    <div class="flex items-end gap-3">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>

        {{-- Select com chevron e foco azul; valores em EN, rótulos em PT-BR --}}
        <div class="relative">
          <select wire:model.live="status"
                  class="appearance-none rounded-xl border-slate-200 pr-9 px-3 py-2 text-sm shadow-sm
                         focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600">
            @foreach($statusOptions as $value => $label)
              <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
          </select>
          <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"
               viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M6 9l6 6 6-6"/>
          </svg>
        </div>
      </div>

      @if($status)
        <button type="button"
                wire:click="$set('status','')"
                class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm
                       bg-slate-100 hover:bg-slate-200">
          Limpar
        </button>
      @endif
    </div>
  </div>

  {{-- Tabela --}}
  <div class="mt-4 overflow-x-auto rounded-xl ring-1 ring-slate-200">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50/80">
        <tr class="text-left text-slate-600">
          <th class="px-4 py-3 font-medium">ID</th>
          <th class="px-4 py-3 font-medium">Transferência</th>
          <th class="px-4 py-3 font-medium">Destinatário</th>
          <th class="px-4 py-3 font-medium">Status</th>
          <th class="px-4 py-3 font-medium">Tentativas</th>
          <th class="px-4 py-3 font-medium">Atualizado</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-200 bg-white">
        @forelse($rows as $r)
          @php
            $st        = strtoupper($r->status ?? '');
            $badge     = $badgeMap[$st] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
            $labelPTBR = $statusOptions[$st] ?? ucfirst(strtolower($st)); // fallback elegante
          @endphp

          <tr class="hover:bg-slate-50/70" wire:key="notif-{{ $r->id }}">
            <td class="px-4 py-3 font-mono text-slate-700">{{ $r->id }}</td>

            <td class="px-4 py-3">
              <div class="font-medium text-slate-900">#{{ $r->transfer_id ?? '—' }}</div>
              @if($r->transfer_id)
                <div class="text-xs text-slate-500">referência</div>
              @endif
            </td>

            <td class="px-4 py-3">
              <div class="font-medium text-slate-900">{{ $r->receiver?->name ?? '—' }}</div>
              @if($r->receiver?->email)
                <div class="text-xs text-slate-500">{{ $r->receiver->email }}</div>
              @endif
            </td>

            <td class="px-4 py-3">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 {{ $badge }}">
                {{ $labelPTBR }}
              </span>
            </td>

            <td class="px-4 py-3">
              <span class="font-medium text-slate-900">{{ $r->attempts }}</span>
            </td>

            <td class="px-4 py-3 text-slate-700">
              {{ $r->updated_at->format('d/m/Y H:i') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-10">
              <div class="text-center">
                <div class="mx-auto h-10 w-10 rounded-xl bg-slate-100 grid place-items-center text-slate-500">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/>
                  </svg>
                </div>
                <p class="mt-2 text-sm text-slate-500">Sem notificações.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginação / resumo --}}
  <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
    <div class="text-xs text-slate-500">
      Mostrando
      <span class="font-medium text-slate-700">{{ $rows->firstItem() ?? 0 }}</span>
      a
      <span class="font-medium text-slate-700">{{ $rows->lastItem() ?? 0 }}</span>
      de
      <span class="font-medium text-slate-700">{{ $rows->total() }}</span>
      resultados
    </div>
    {{ $rows->links() }}
  </div>
</div>
