<x-layout>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

        <div class="relative p-6 rounded-lg shadow-lg mb-8 z-10 bg-gradient-to-r from-[#FFF2F2] to-[#A9B5DF]">
            <!-- Circular Gradient Background -->
            <div class="absolute inset-0 rounded-lg overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-[#FFF2F2] via-[#E8ECF5] via-[#DBE0F0] to-[#7886C7] opacity-80"></div>

                <!-- Subtle Decorative Elements -->
                <div class="absolute top-0 right-0 w-full h-full bg-gradient-to-bl from-[#A9B5DF]/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 w-full h-full bg-gradient-to-tr from-[#2D336B]/10 to-transparent"></div>
            </div>

            <!-- Greeting Content -->
            <div class="relative z-10">
                <h2 class="text-3xl font-semibold text-[#2D336B]">Halo, {{ $role }}!</h2>
                <p class="mt-2 text-md text-gray-600">
                    @if($role === 'Superadmin')
                        Selamat datang di dashboard Superadmin. Anda memiliki akses penuh untuk mengelola sistem dan data pengguna.
                    @elseif($role === 'Admin')
                        Selamat datang di dashboard Admin. Anda dapat mengelola data dan konten sistem.
                    @elseif($role === 'Operator')
                        Selamat datang di dashboard Operator. Anda dapat menginput dan memperbarui data.
                    @else
                        Selamat datang di dashboard User.
                    @endif
                    Silakan gunakan menu di samping untuk navigasi ke berbagai fitur yang tersedia.
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total Super Admin</h3>
                        <p class="mt-1 text-2xl font-bold text-[#7886C7]">{{ $userStats['superadmin'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Pengelola Utama</p>
                    </div>
                    <div class="text-[#2D336B]">
                        <i class="fas fa-user-shield text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total Admin Desa</h3>
                        <p class="mt-1 text-2xl font-bold text-[#7886C7]">{{ $userStats['admin'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Administrator Desa</p>
                    </div>
                    <div class="text-[#2D336B]">
                        <i class="fas fa-user-tie text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total Operator Desa</h3>
                        <p class="mt-1 text-2xl font-bold text-[#7886C7]">{{ $userStats['operator'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Petugas Input Data</p>
                    </div>
                    <div class="text-[#2D336B]">
                        <i class="fas fa-user-cog text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total User</h3>
                        <p class="mt-1 text-2xl font-bold text-[#7886C7]">{{ $userStats['user'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Pengguna Terdaftar</p>
                    </div>
                    <div class="text-[#2D336B]">
                        <i class="fas fa-users text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total Penduduk</h3>
                        <p class="mt-1 text-2xl font-bold {{ $totalCitizens > 0 ? 'text-[#2D336B]' : 'text-red-500' }}">
                            {{ $totalCitizens > 0 ? number_format($totalCitizens) : 'Data tidak tersedia' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Data Kependudukan</p>
                    </div>
                    <div class="text-[#2D336B]">
                        <i class="fas fa-id-card text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Pie Chart - Heads of Family Comparison -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Data Penduduk</h3>
                <div class="h-64">
                    @if($totalCitizens > 0)
                        <canvas id="familyHeadChart"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500">Data tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Monthly Registration Chart -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Registrasi Pengguna Bulanan</h3>
                <div class="h-64">
                    <canvas id="monthlyRegistrationChart"></canvas>
                </div>
                <p class="text-xs text-gray-500 text-center mt-2">Data registrasi pengguna tahun {{ date('Y') }}</p>
            </div>
        </div>

        <!-- Charts Initialization -->
        <script>
            @if($totalCitizens > 0)
            // Pie Chart for Family Heads
            const familyHeadCtx = document.getElementById('familyHeadChart').getContext('2d');
            const familyHeadChart = new Chart(familyHeadCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Kepala Keluarga', 'Anggota Keluarga'],
                    datasets: [{
                        data: [{{ $headsOfFamily }}, {{ $totalCitizens - $headsOfFamily }}],
                        backgroundColor: [
                            '#7886C7', // Medium blue-purple from gradient
                            '#A9B5DF'  // Light blue-purple from gradient
                        ],
                        borderColor: [
                            '#2D336B', // Dark blue from text color
                            '#FFF2F2'  // Light pink from gradient
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            @endif

            // Monthly Registration Chart
            const monthlyCtx = document.getElementById('monthlyRegistrationChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyData['labels']) !!},
                    datasets: [
                        {
                            label: 'Superadmin',
                            data: {!! json_encode($monthlyData['superadmin']) !!},
                            backgroundColor: 'rgba(45, 51, 107, 0.3)',
                            borderColor: '#2D336B', // Dark blue
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Admin',
                            data: {!! json_encode($monthlyData['admin']) !!},
                            backgroundColor: 'rgba(120, 134, 199, 0.3)',
                            borderColor: '#7886C7', // Medium blue-purple
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Operator',
                            data: {!! json_encode($monthlyData['operator']) !!},
                            backgroundColor: 'rgba(169, 181, 223, 0.3)',
                            borderColor: '#A9B5DF', // Light blue-purple
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'User',
                            data: {!! json_encode($monthlyData['user']) !!},
                            backgroundColor: 'rgba(255, 242, 242, 0.3)',
                            borderColor: '#FF9A9E', // Light pink
                            borderWidth: 2,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // Only show whole numbers
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y;
                                    return `${label}: ${value} pengguna`;
                                }
                            }
                        }
                    }
                }
            });
        </script>
    </div>
</x-layout>

