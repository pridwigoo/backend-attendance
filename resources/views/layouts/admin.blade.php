<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
    <nav class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center shadow-md">
        <h1 class="text-xl font-bold">Attendance Admin Web</h1>
        <div class="flex items-center gap-4">
            <span>{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm font-semibold">Logout</button>
            </form>
        </div>
    </nav>

    <div class="flex min-h-screen">
        <aside class="w-64 bg-white shadow-md p-4">
            <ul class="space-y-2 font-medium">
                <li><a href="{{ route('admin.dashboard') }}" class="block p-2 text-gray-700 hover:bg-blue-50 rounded">Dashboard</a></li>
                <li><a href="{{ route('admin.employees.index') }}" class="block p-2 text-blue-600 font-bold bg-blue-50 rounded">Employee Approval</a></li>
            </ul>
        </aside>

        <main class="flex-1 p-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>

</html>