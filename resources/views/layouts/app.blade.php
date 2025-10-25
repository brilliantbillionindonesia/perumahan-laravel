<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="h-full text-gray-800">

    <div class="min-h-full">

        <!-- Navbar -->
        <nav class="bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500"
                            alt="Logo" class="h-8 w-8" />

                        <!-- Menu -->
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ route('admin.dashboard') }}"
                                class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white">
                                Dashboard
                            </a>
                            <a href="{{ route('housings.index') }}"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                Perumahan
                            </a>
                            <a href="#"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @yield('content')
        </main>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success') || session('updated') || session('deleted'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    background: '#f8fafc',
                    color: '#1e293b',
                    iconColor: '#16a34a',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                @if (session('success'))
                    Toast.fire({
                        icon: 'success',
                        title: '{{ session('success') }}'
                    });
                @elseif (session('updated'))
                    Toast.fire({
                        icon: 'info',
                        title: '{{ session('updated') }}'
                    });
                @elseif (session('deleted'))
                    Toast.fire({
                        icon: 'warning',
                        title: '{{ session('deleted') }}'
                    });
                @endif
            });
        </script>
    @endif
    @stack('scripts')
</body>

</html>
