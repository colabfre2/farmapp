<nav class="bg-white-shadow px-6 py-4 flex justify-between items-center">
    <h1 class="text-2x1 font-bold text-green-600">
        Farm app
    </h1>
    <div class="flex items-center gap-4">
        <span class="text-gray-700">
            {{ auth()->user()->name }}
        </span>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" onclick="return confirm('Apakah kamu yakin ingin logout?')" class=bg-red-500
                hover:bg-red-600 text-white px-4 py-2 rounded-lg transition>logout</button>
        </form>
    </div>
</nav>
