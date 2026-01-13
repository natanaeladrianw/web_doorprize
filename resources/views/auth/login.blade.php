<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Doorprize</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">

        <!-- Header -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-800">
                Doorprize Admin
            </h2>
            <p class="text-slate-500 text-sm mt-1">
                Sistem Manajemen Doorprize
            </p>
        </div>

        <!-- Error -->
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200
                        text-red-600 text-sm p-3">
                Email atau password salah.
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember"
                           class="rounded border-slate-300 text-indigo-600">
                    Ingat saya
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-indigo-600 hover:underline">
                        Lupa password?
                    </a>
                @endif
            </div> --}}

            <button
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700
                       text-white font-semibold py-2.5 rounded-lg transition">
                LOGIN
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-400">
            © {{ date('Y') }} Doorprize System
        </div>
    </div>

</body>
</html>
