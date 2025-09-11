<div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-sm ring-1 ring-slate-200">
  {{-- Filtros / Toolbar --}}
  <div class="flex flex-col sm:flex-row sm:items-end gap-3">
    <div class="flex-1">
      <label class="block text-sm font-medium text-slate-700 mb-1">Buscar</label>
      <input type="text" wire:model.live.debounce.300ms="search"
             class="w-full rounded-xl border-slate-200 px-3 py-2 shadow-sm
                    focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900"
             placeholder="Nome ou e-mail...">
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Perfil</label>
      <select wire:model.live="role"
              class="w-44 rounded-xl border-slate-200 px-3 py-2 shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900">
        <option value="">Todos</option>
        <option value="user">Usuário</option>
        <option value="merchant">Lojista</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Itens por página</label>
      <select wire:model.live="perPage"
              class="w-28 rounded-xl border-slate-200 px-3 py-2 shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-900">
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
          <th class="px-4 py-3 font-medium">Nome</th>
          <th class="px-4 py-3 font-medium">E-mail</th>
          <th class="px-4 py-3 font-medium">Perfil</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200 bg-white">
        @forelse($rows as $u)
          @php
            $badge = $u->role === 'merchant'
              ? 'bg-violet-50 text-violet-700 ring-violet-200'
              : 'bg-sky-50 text-sky-700 ring-sky-200';
          @endphp
          <tr class="hover:bg-slate-50/70">
            <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
            <td class="px-4 py-3 text-slate-700">{{ $u->email }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $badge }}">
                {{ strtoupper($u->role) }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-4 py-10">
              <div class="text-center">
                <div class="mx-auto h-10 w-10 rounded-xl bg-slate-100 grid place-items-center text-slate-500">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/>
                  </svg>
                </div>
                <p class="mt-2 text-sm text-slate-500">Nenhum usuário encontrado.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Rodapé de paginação --}}
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

    {{-- mantém o layout custom, mas com hooks do Livewire --}}
    {{ $rows->onEachSide(1)->links('components.pagination') }}
  </div>
</div>
