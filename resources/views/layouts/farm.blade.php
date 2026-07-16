<!DOCTYPE html>
<html>

<head>
    <title>Farm App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <div class="flex">
        @include('layouts.sidebar')
        <div class="flex-1">
            @include('layouts.navbar')
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
<script>
    function toggleMasterData() {
        const menu = document.getElementById('masterDataMenu');
        const icon = document.getElementById('masterDataIcon');

        menu.classList.toggle('hidden');
        icon.classList.toggle('rotate-90');
    }
</script>

</html>
