@props([
    'name',               // nome do input hidden (ex: client_id)
    'value' => null,      // valor atual (id)
    'lookupUrl',          // rota endpoint ex: route('lookups.clients', ['type' => 'PF'])
    'placeholder' => 'Digite para buscar...',
    'label' => null,
])

@php
    $inputId = 'ac_' . md5($name . '-' . uniqid('', true));
@endphp

<div class="relative">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}_hidden" value="{{ old($name, $value) }}">

    <input type="text"
           id="{{ $inputId }}_query"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

    <div id="{{ $inputId }}_list"
         class="absolute z-20 mt-1 hidden w-full rounded-md border border-gray-200 bg-white shadow">
    </div>

    <p id="{{ $inputId }}_selected" class="mt-1 text-xs text-gray-500"></p>
</div>

<script>
(function () {
    const lookupUrl = @json($lookupUrl);
    const hidden = document.getElementById(@json($inputId + '_hidden'));
    const query = document.getElementById(@json($inputId + '_query'));
    const list = document.getElementById(@json($inputId + '_list'));
    const selected = document.getElementById(@json($inputId + '_selected'));

    let timer = null;

    function showList() { list.classList.remove('hidden'); }
    function hideList() { list.classList.add('hidden'); list.innerHTML = ''; }

    function setSelected(item) {
        hidden.value = item.id;
        query.value = item.label;
        selected.textContent = `Selecionado: ${item.label}`;
        hideList();
    }

    async function fetchItems(q) {
        const url = new URL(lookupUrl, window.location.origin);
        url.searchParams.set('q', q);

        const res = await fetch(url.toString(), {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return [];
        const json = await res.json();
        return json.data || [];
    }

    function render(items) {
        if (!items.length) {
            list.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500">Nenhum resultado.</div>`;
            showList();
            return;
        }

        list.innerHTML = items.map((it) => `
            <button type="button"
                    class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-50"
                    data-id="${it.id}"
                    data-label="${it.label.replaceAll('"', '&quot;')}">
                ${it.label}
            </button>
        `).join('');

        list.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                setSelected({ id: btn.dataset.id, label: btn.dataset.label });
            });
        });

        showList();
    }

    query.addEventListener('input', () => {
        clearTimeout(timer);
        const q = query.value.trim();

        if (q.length < 2) {
            hideList();
            return;
        }

        timer = setTimeout(async () => {
            const items = await fetchItems(q);
            render(items);
        }, 250);
    });

    query.addEventListener('focus', () => {
        // se já tem texto, permite reabrir e buscar
        const q = query.value.trim();
        if (q.length >= 2) query.dispatchEvent(new Event('input'));
    });

    document.addEventListener('click', (e) => {
        if (!list.contains(e.target) && e.target !== query) {
            hideList();
        }
    });
})();
</script>