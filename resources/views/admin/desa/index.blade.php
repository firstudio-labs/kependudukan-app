<x-layout>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Admin Desa {{ $villageName }}</h1>

        <div class="relative p-6 rounded-lg shadow-lg mb-8 z-10 bg-gradient-to-r from-[#FFF2F2] to-[#A9B5DF]">
            <div class="relative z-10">
                <h2 class="text-3xl font-semibold text-[#2D336B]">Halo, {{ $role ?? 'Admin Desa' }},
                    {{ $villageName ?? 'Desa Anda' }}!</h2>
                <p class="mt-2 text-md text-gray-600">
                    @if (isset($role) && $role === 'Admin desa')
                        Selamat datang di dashboard Admin Desa {{ $villageName ?? 'Desa Anda' }}.
                        Anda memiliki akses penuh untuk mengelola sistem dan data pengguna di desa ini.
                    @else
                        Silakan gunakan menu di samping untuk navigasi ke berbagai fitur yang tersedia.
                    @endif
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total Admin Desa</h3>
                        <p class="mt-1 text-2xl font-bold text-[#7886C7]">{{ $userStats['admin'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Administrator Desa</p>
                    </div>
                    <div class="text-[#2D336B]">
                        <i class="fas fa-user-tie text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total User</h3>
                        <p class="mt-1 text-2xl font-bold text-[#7886C7]">{{ $userStats['user'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Pengguna Terdaftar di Desa Ini</p>
                    </div>
                    <div class="text-[#2D336B]">
                        <i class="fas fa-users text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div> --}}

            <div class="bg-white p-4 rounded-lg shadow-md transition-transform hover:scale-105">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700">Total Penduduk</h3>
                        <p
                            class="mt-1 text-2xl font-bold {{ isset($totalCitizens) && $totalCitizens > 0 ? 'text-[#2D336B]' : 'text-red-500' }}">
                            {{ isset($totalCitizens) && $totalCitizens > 0 ? number_format($totalCitizens) : 'Data tidak tersedia' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Data Kependudukan {{ $villageName }}</p>
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
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Data Penduduk {{ $villageName }}</h3>
                <div class="h-64">
                    @if (isset($totalCitizens) && $totalCitizens > 0 && isset($headsOfFamily))
                        <canvas id="familyHeadChart"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500">Data tidak tersedia atau sedang dimuat</p>
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
                <p class="text-xs text-gray-500 text-center mt-2">Data registrasi pengguna tahun {{ date('Y') }}
                </p>
            </div>
        </div>

        @if (isset($genderStats) && !empty($genderStats))
            <!-- Additional Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Gender Distribution Chart -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Distribusi Gender {{ $villageName }}</h3>
                    <div class="h-64">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>

                <!-- Age Distribution Chart -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Distribusi Usia {{ $villageName }}</h3>
                    <div class="h-64">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

        <!-- Charts Initialization -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (isset($totalCitizens) && $totalCitizens > 0 && isset($headsOfFamily))
                    // Pie Chart for Family Heads
                    const familyHeadCtx = document.getElementById('familyHeadChart').getContext('2d');
                    const familyHeadChart = new Chart(familyHeadCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Kepala Keluarga', 'Anggota Keluarga'],
                            datasets: [{
                                data: [
                                    {{ $headsOfFamily }},
                                    {{ $totalCitizens - $headsOfFamily > 0 ? $totalCitizens - $headsOfFamily : 0 }}
                                ],
                                backgroundColor: [
                                    '#7886C7', // Medium blue-purple from gradient
                                    '#A9B5DF' // Light blue-purple from gradient
                                ],
                                borderColor: [
                                    '#2D336B', // Dark blue from text color
                                    '#FFF2F2' // Light pink from gradient
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
                                            const percentage = total > 0 ? Math.round((value / total) *
                                                100) : 0;
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                @endif

                @if (isset($genderStats) && !empty($genderStats))
                    // Gender Distribution Chart
                    const genderCtx = document.getElementById('genderChart').getContext('2d');
                    const genderChart = new Chart(genderCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Laki-laki', 'Perempuan'],
                            datasets: [{
                                data: [
                                    {{ $genderStats['male'] ?? 0 }},
                                    {{ $genderStats['female'] ?? 0 }}
                                ],
                                backgroundColor: [
                                    '#4B6BDC', // Blue for male
                                    '#FF6CA8' // Pink for female
                                ],
                                borderColor: [
                                    '#2D336B',
                                    '#FFF2F2'
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
                                            const percentage = total > 0 ? Math.round((value / total) *
                                                100) : 0;
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Age Distribution Chart
                    const ageCtx = document.getElementById('ageChart').getContext('2d');
                    const ageChart = new Chart(ageCtx, {
                        type: 'bar',
                        data: {
                            labels: ['0-17', '18-30', '31-45', '46-60', '61+'],
                            datasets: [{
                                label: 'Jumlah Penduduk',
                                data: [
                                    {{ $ageStats['0-17'] ?? 0 }},
                                    {{ $ageStats['18-30'] ?? 0 }},
                                    {{ $ageStats['31-45'] ?? 0 }},
                                    {{ $ageStats['46-60'] ?? 0 }},
                                    {{ $ageStats['61+'] ?? 0 }}
                                ],
                                backgroundColor: '#7886C7',
                                borderColor: '#2D336B',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                @endif

                // Monthly Registration Chart
                try {
                    const monthlyCtx = document.getElementById('monthlyRegistrationChart').getContext('2d');
                    const monthlyData = {
                        labels: {!! json_encode(
                            $monthlyData['labels'] ??
                                array_map(function ($m) {
                                    return date('M', mktime(0, 0, 0, $m, 1));
                                }, range(1, 12)),
                        ) !!},
                        datasets: [{
                                label: 'Superadmin',
                                data: {!! json_encode($monthlyData['superadmin'] ?? array_fill(0, 12, 0)) !!},
                                backgroundColor: 'rgba(45, 51, 107, 0.3)',
                                borderColor: '#2D336B', // Dark blue
                                borderWidth: 2,
                                tension: 0.3
                            },
                            {
                                label: 'Admin',
                                data: {!! json_encode($monthlyData['admin'] ?? array_fill(0, 12, 0)) !!},
                                backgroundColor: 'rgba(120, 134, 199, 0.3)',
                                borderColor: '#7886C7', // Medium blue-purple
                                borderWidth: 2,
                                tension: 0.3
                            },
                            {
                                label: 'Operator',
                                data: {!! json_encode($monthlyData['operator'] ?? array_fill(0, 12, 0)) !!},
                                backgroundColor: 'rgba(169, 181, 223, 0.3)',
                                borderColor: '#A9B5DF', // Light blue-purple
                                borderWidth: 2,
                                tension: 0.3
                            },
                            {
                                label: 'User ',
                                data: {!! json_encode($monthlyData['user'] ?? array_fill(0, 12, 0)) !!},
                                backgroundColor: 'rgba(255, 242, 242, 0.3)',
                                borderColor: '#FF9A9E', // Light pink
                                borderWidth: 2,
                                tension: 0.3
                            }
                        ]
                    };

                    const monthlyChart = new Chart(monthlyCtx, {
                        type: 'line',
                        data: monthlyData,
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
                } catch (error) {
                    console.error('Error initializing monthly chart:', error);
                }
            });
        </script>
    </div>
</x-layout>
