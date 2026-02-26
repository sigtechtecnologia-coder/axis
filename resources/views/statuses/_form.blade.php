@php
    $isEdit = $status->exists;
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-gray-700">Contexto</label>
        <select name="context" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @disabled($isEdit)>
            @foreach ($contexts as $key => $label)
                <option value="{{ $key }}" @selected(old('context', $status->context) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('context') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        @if($isEdit)
            <p class="mt-1 text-xs text-gray-500">O contexto não pode ser alterado depois.</p>
        @endif
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Nome</label>
        <input type="text" name="name" value="{{ old('name', $status->name) }}"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="Ex: Enviado, Em andamento..." />
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Ordem</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $status->sort_order) }}"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Cor (hex)</label>
        <input type="text" name="color" value="{{ old('color', $status->color) }}"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="#0284c7" />
        @error('color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        <p class="mt-1 text-xs text-gray-500">Opcional. Ex: #16a34a</p>
    </div>

    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                   @checked(old('is_active', $status->is_active) ? true : false)>
            <span class="text-sm font-medium text-gray-700">Ativo</span>
        </label>
        @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        <p class="mt-1 text-xs text-gray-500">Para “remover”, desmarque Ativo (inativar).</p>
    </div>
</div>