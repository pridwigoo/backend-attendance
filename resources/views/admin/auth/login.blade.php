<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Admin Login</h2>
        @if($errors->any())
        <div class="bg-red-100 text-red-600 p-2 rounded mb-4 text-sm">
            {{ $errors->first() }}
        </div>
        @endif
        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-1 text-sm">Email</label>
                <input type="email" name="email" class="w-full border px-3 py-2 rounded focus:outline-blue-500" required>
            </div>
            <div class="mb-6">
                <label class="block mb-1 text-sm">Password</label>
                <input type="password" name="password" class="w-full border px-3 py-2 rounded focus:outline-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">Login</button>
        </form>
    </div>
</body>

</html>