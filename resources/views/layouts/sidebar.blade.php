<aside class="w-64 bg-slate-900 text-white min-h-screen">
    <div class="p-6 border-b border-slate-700">
        <h1 class="text-2x1 font-bold">
            Farm app
        </h1>

    </div>
    <nav class="p-4">
        <a href="/dashboard" class="block px-4 py-4 rounded-lg hover:bg-slate-800 mb-2">
            Dashboard
        </a>

        <div class="mt-4">
            <button onclick="toggleMasterData()"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">

                <svg id="masterDataIcon" class="w-6 h-6 transition-transform duration-500 ease-in-out"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>

                <span>Master Data</span>

            </button>
            <div id="masterDataMenu" class="ml-4 mt-2">
                <a href="{{ route('categories.index') }}"
                    class="block py-2 px-3 rounded {{ request()->routeIs('categories.*') ? 'bg-green-600 text-white' : 'hover:bg-slate-800' }}">categories</a>

                <a href="{{ Route('crop-types.index') }}"
                    class="block py-2 px-3 rounded {{ request()->routeIs('crop-types.*') ? 'bg-green-600 text-white' : 'hover:bg-slate-800' }}">Crop
                    Types</a>

                <a href="#"
                    class="block py-2 px-3 rounded {{ request()->routeIs('livestock-types.*') ? 'bg-green-600 text-white' : 'hover:bg-slate-800' }}">Livestock
                    Types</a>

                <a href="#"
                    class="block py-2 px-3 rounded {{ request()->routeIs('units.*') ? 'bg-green-600 text-white' : 'hover:bg-slate-800' }}">Units</a>

                <a href="#"
                    class="block py-2 px-3 rounded {{ request()->routeIs('expanse-categories.*') ? 'bg-green-600 text-white' : 'hover:bg-slate-800' }}">Expanse
                    categories</a>
            </div>

        </div>
    </nav>
</aside>
