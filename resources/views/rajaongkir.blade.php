<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Raja Ongkir V2 - SantriKoding.com</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
    <style>
        .loader{border:4px solid #f3f3f3;border-top:4px solid #4f46e5;border-radius:50%;width:30px;height:30px;animation:spin 1s linear infinite;margin:0 auto;display:none}@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
    </style>
</head>
<body class="bg-gray-200 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-xl shadow w-full max-w-2xl">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Kalkulator Ongkos Kirim (V2)</h1>

        <div class="mb-8 relative">
            <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">Cari Kota / Kecamatan Tujuan</label>
            <input type="text" id="destination" autocomplete="off"
                placeholder="Ketik minimal 3 huruf, contoh: Cengkareng..."
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base bg-gray-100 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow">
            <div id="destinationResults" class="absolute w-full bg-white border border-gray-200 rounded-md shadow mt-1 hidden max-h-64 overflow-y-auto z-10"></div>
            <input type="hidden" id="destinationId" value="">
        </div>

    </div>

    <script>
        const SEARCH_URL = "{{ route('rajaongkir.search') }}";
        let searchTimeout = null;

        const input   = document.getElementById('destination');
        const results = document.getElementById('destinationResults');

        input.addEventListener('input', function () {
            const keyword = this.value.trim();
            clearTimeout(searchTimeout);
            document.getElementById('destinationId').value = '';

            if (keyword.length < 3) {
                results.classList.add('hidden');
                results.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`${SEARCH_URL}?q=${encodeURIComponent(keyword)}`)
                    .then(r => r.json())
                    .then(data => renderResults(data))
                    .catch(() => {
                        results.innerHTML = '<div class="p-2 text-sm text-gray-500">Gagal mencari lokasi.</div>';
                        results.classList.remove('hidden');
                    });
            }, 350);
        });

        function renderResults(data) {
            results.innerHTML = '';

            if (!data || data.length === 0) {
                results.innerHTML = '<div class="p-2 text-sm text-gray-500">Lokasi tidak ditemukan.</div>';
                results.classList.remove('hidden');
                return;
            }

            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'p-2 text-sm hover:bg-indigo-50 cursor-pointer border-b border-gray-100';
                div.textContent = item.label || `${item.district_name}, ${item.city_name}, ${item.province_name}`;
                div.addEventListener('click', () => {
                    input.value = div.textContent;
                    document.getElementById('destinationId').value = item.id;
                    results.classList.add('hidden');
                    results.innerHTML = '';
                });
                results.appendChild(div);
            });

            results.classList.remove('hidden');
        }

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                results.classList.add('hidden');
            }
        });
    </script>
</body>
</html>