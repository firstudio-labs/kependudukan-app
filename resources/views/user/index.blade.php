<x-layout>
    @php
        // Check if $role is defined, if not get it from Auth user
        if (!isset($role)) {
            // Check which guard is active
            if (Auth::guard('penduduk')->check()) {
                $user = Auth::guard('penduduk')->user();
                $role = 'Penduduk'; // Set default role for penduduk users
            } else {
                $user = Auth::guard('web')->user();
                $role = ucfirst($user->role);
            }
        }
    @endphp

    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

        <div class="relative p-6 rounded-lg shadow-lg mb-8 z-10 bg-gradient-to-r from-[#FFF2F2] to-[#A9B5DF]">
            <div class="absolute inset-0 rounded-lg overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-[#FFF2F2] via-[#E8ECF5] via-[#DBE0F0] to-[#7886C7] opacity-80">
                </div>

                <!-- Subtle Decorative Elements -->
                <div class="absolute top-0 right-0 w-full h-full bg-gradient-to-bl from-[#A9B5DF]/20 to-transparent">
                </div>
                <div class="absolute bottom-0 left-0 w-full h-full bg-gradient-to-tr from-[#2D336B]/10 to-transparent">
                </div>
            </div>

            <!-- Greeting Content -->
            <div class="relative z-10">
                <h2 class="text-3xl font-semibold text-[#2D336B]">Halo, {{ $role }}!</h2>
                <p class="mt-2 text-md text-gray-600">
                    @if($role === 'Superadmin')
                        Selamat datang di dashboard Superadmin. Anda memiliki akses penuh untuk mengelola sistem dan data
                        pengguna.
                    @elseif($role === 'Admin')
                        Selamat datang di dashboard Admin. Anda dapat mengelola data dan konten sistem.
                    @elseif($role === 'Operator')
                        Selamat datang di dashboard Operator. Anda dapat menginput dan memperbarui data.
                    @elseif($role === 'Penduduk')
                        Selamat datang di dashboard Penduduk. Anda dapat mengakses layanan dan informasi kependudukan.
                    @else
                        Selamat datang di dashboard User.
                    @endif
                    Silakan gunakan menu di samping untuk navigasi ke berbagai fitur yang tersedia.
                </p>
            </div>
        </div>

        <!-- Add more dashboard content here -->
    </div>
</x-layout>