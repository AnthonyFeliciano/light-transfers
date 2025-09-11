<div class="space-y-6">
  @php
    $cutDate  = now()->subDays(15);
    $cutShown = false;
  @endphp

  {{-- CARD: Saldo --}}
  <div class="rounded-2xl p-8 lg:p-10 shadow-sm ring-1 ring-sky-700/20 bg-gradient-to-r from-sky-600 to-indigo-600 text-white">
    <div class="flex items-end justify-between gap-6">
      <div>
        <div class="text-xs uppercase tracking-wide text-white/80">Saldo em carteira</div>
        <div class="mt-2 text-5xl md:text-6xl font-semibold tracking-tight text-white">
          R$ {{ number_format($balance, 2, ',', '.') }}
        </div>
      </div>
      <span class="hidden sm:inline-flex h-9 items-center rounded-full px-3 text-sm bg-white/15 text-white ring-1 ring-white/20">
        Atualizado agora
      </span>
    </div>
  </div>

  {{-- MOVIMENTAÇÕES --}}
  <div class="rounded-2xl bg-white/80 backdrop-blur p-6 shadow-sm ring-1 ring-slate-200">
    {{-- Filtros --}}
    <div class="flex flex-wrap items-end gap-3">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
        <select wire:model.live="type"
                class="rounded-xl border-slate-200 px-3 py-2 text-sm shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600">
          <option value="">Todos</option>
          <option value="DEBIT">Débito</option>
          <option value="CREDIT">Crédito</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">De</label>
        <input type="date" wire:model.live="from"
               class="rounded-xl border-slate-200 px-3 py-2 text-sm shadow-sm
                      focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600">
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Até</label>
        <input type="date" wire:model.live="to"
               class="rounded-xl border-slate-200 px-3 py-2 text-sm shadow-sm
                      focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600">
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Itens/página</label>
        <select wire:model.live="perPage"
                class="w-28 rounded-xl border-slate-200 px-3 py-2 text-sm shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600">
          @foreach([10,15,25,50] as $n)
            <option value="{{ $n }}">{{ $n }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Tabela --}}
    <div class="mt-4 overflow-x-auto rounded-xl ring-1 ring-slate-200">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50/80">
          <tr class="text-left text-slate-600">
            <th class="px-4 py-3 font-medium">Data</th>
            <th class="px-4 py-3 font-medium">Tipo</th>
            <th class="px-4 py-3 font-medium">Transferência</th>
            <th class="px-4 py-3 font-medium text-right">Valor</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
          @if ($rows->count() > 0)
            @foreach ($rows as $r)
              {{-- Separador "15 dias atrás" --}}
              @if (!$cutShown && $r->created_at->lt($cutDate))
                @php $cutShown = true; @endphp
                <tr>
                  <td colspan="4" class="px-4 py-2">
                    <div class="relative my-1">
                      <div class="h-px w-full bg-slate-200"></div>
                      <div class="absolute inset-0 -top-3 grid place-items-center">
                        <span class="inline-flex items-center rounded-full bg-slate-900 text-white px-2.5 py-0.5 text-xs shadow-sm">
                          15 dias atrás
                        </span>
                      </div>
                    </div>
                  </td>
                </tr>
              @endif

              @php
                $userId = auth()->id();

                // Direção: preferir dados da transferência; fallback para o type
                $hasTransfer = isset($r->transfer_id) && $r->relationLoaded('transfer') && $r->transfer;
                if ($hasTransfer) {
                    $isOut = ($r->transfer->payer_id === $userId); // você enviou
                } else {
                    $rawType = strtoupper((string)($r->type ?? ''));
                    $isOut   = in_array($rawType, ['DEBIT','D','DEBITO','DÉBITO'], true);
                }

                $sign   = $isOut ? '-' : '+';
                $amount = $sign.' R$ '.number_format(abs((float)$r->amount), 2, ',', '.');

                $valCls    = $isOut ? 'text-rose-600'     : 'text-emerald-600';
                $badge     = $isOut ? 'bg-rose-50 text-rose-700 ring-rose-200'
                                    : 'bg-emerald-50 text-emerald-700 ring-emerald-200';
                $typeLabel = $isOut ? 'Débito' : 'Crédito';
              @endphp

              <tr class="hover:bg-slate-50/70" wire:key="row-{{ $r->id ?? $r->created_at->timestamp }}-{{ $loop->index }}">
                <td class="px-4 py-3 text-slate-700">{{ $r->created_at->format('d/m/Y H:i') }}</td>

                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 {{ $badge }}">
                    {{ $typeLabel }}
                  </span>
                </td>

                <td class="px-4 py-3 text-slate-600">{{ $r->transfer_id ?? '—' }}</td>

                <td class="px-4 py-3 text-right font-medium {{ $valCls }}">{{ $amount }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="4" class="px-4 py-10">
                <div class="text-center">
                  <div class="mx-auto h-10 w-10 rounded-xl bg-slate-100 grid place-items-center text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/>
                    </svg>
                  </div>
                  <p class="mt-2 text-sm text-slate-500">Sem lançamentos.</p>
                </div>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    {{-- Paginação (padrão custom) --}}
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

      {{-- mesmo componente de paginação usado nas outras telas --}}
      {{ $rows->onEachSide(1)->links('components.pagination') }}
    </div>
  </div>
</div>
