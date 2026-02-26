@php
    $types = $types ?? ['PF' => 'Pessoa Física', 'PJ' => 'Pessoa Jurídica'];
@endphp

<div class="grid grid-cols-1 gap-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo</label>
            <select id="client_type" name="type"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type', $client->type) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end">
            <label class="inline-flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       @checked(old('is_active', $client->is_active) ? true : false)>
                <span class="text-sm font-medium text-gray-700">Ativo</span>
            </label>
            @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- PF --}}
    <div id="panel_pf" class="rounded-md border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Pessoa Física</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome completo</label>
                <input type="text" name="full_name" value="{{ old('full_name', $client->full_name) }}"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">CPF</label>
                <input id="cpf" type="text" name="cpf" value="{{ old('cpf', $client->cpf) }}"
                       placeholder="000.000.000-00"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('cpf') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- PJ --}}
    <div id="panel_pj" class="rounded-md border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Pessoa Jurídica</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Razão social</label>
                <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input id="cnpj" type="text" name="cnpj" value="{{ old('cnpj', $client->cnpj) }}"
                       placeholder="00.000.000/0000-00"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('cnpj') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Responsável</label>
                <input type="text" name="responsible_name" value="{{ old('responsible_name', $client->responsible_name) }}"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('responsible_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">CPF do responsável</label>
                <input id="responsible_cpf" type="text" name="responsible_cpf" value="{{ old('responsible_cpf', $client->responsible_cpf) }}"
                       placeholder="000.000.000-00"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('responsible_cpf') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Contato --}}
    <div class="rounded-md border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Contato</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $client->whatsapp) }}"
                       placeholder="(11) 99999-9999"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('whatsapp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const typeEl = document.getElementById('client_type');
    const pfPanel = document.getElementById('panel_pf');
    const pjPanel = document.getElementById('panel_pj');

    function onlyDigits(v) { return (v || '').replace(/\D+/g, ''); }

    function maskCPF(el) {
        if (!el) return;
        el.addEventListener('input', function () {
            let v = onlyDigits(el.value).slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
            el.value = v;
        });
        // aplica na carga
        el.dispatchEvent(new Event('input'));
    }

    function maskCNPJ(el) {
        if (!el) return;
        el.addEventListener('input', function () {
            let v = onlyDigits(el.value).slice(0, 14);
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            el.value = v;
        });
        el.dispatchEvent(new Event('input'));
    }

    function setPanels() {
        const type = typeEl.value;
        if (type === 'PF') {
            pfPanel.style.display = '';
            pjPanel.style.display = 'none';
        } else {
            pfPanel.style.display = 'none';
            pjPanel.style.display = '';
        }
    }

    typeEl.addEventListener('change', setPanels);
    setPanels();

    maskCPF(document.getElementById('cpf'));
    maskCPF(document.getElementById('responsible_cpf'));
    maskCNPJ(document.getElementById('cnpj'));
})();
</script>