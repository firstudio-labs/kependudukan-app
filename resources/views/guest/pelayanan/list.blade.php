<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portal Layanan {{ $villageName ?? 'Desa' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        custom: {
                            purple: '#969BE7',
                            'purple-hover': '#8084d9', // Slightly darker shade for hover
                        }
                    }
                }
            }
        }
    </script>

    <!-- Flowbite -->
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>

    <!-- Optional: Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(31, 38, 135, 0.1),
                        0 4px 10px rgba(31, 38, 135, 0.05),
                        0 0 0 1px rgba(255, 255, 255, 0.1) inset,
                        0 0 20px rgba(150, 155, 231, 0.1);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(31, 38, 135, 0.15),
                        0 8px 20px rgba(31, 38, 135, 0.1),
                        0 0 0 1px rgba(255, 255, 255, 0.2) inset,
                        0 0 25px rgba(150, 155, 231, 0.2);
            transform: translateY(-2px);
        }

        .text-shadow {
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(150,155,231,0.1));
            box-shadow: 0 8px 16px rgba(31, 38, 135, 0.1);
            margin: 0 auto 0.75rem auto;
            transition: all 0.3s ease;
        }

        .glass-card:hover .icon-circle {
            transform: scale(1.05);
            background: linear-gradient(145deg, rgba(255,255,255,0.3), rgba(150,155,231,0.2));
            box-shadow: 0 10px 20px rgba(31, 38, 135, 0.15);
        }

        .modern-button {
            background: linear-gradient(135deg, #969BE7, #8084d9);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .modern-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
        }

        .glass-card:hover .modern-button::after {
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .footer-button {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 16px rgba(31, 38, 135, 0.1);
            transition: all 0.3s ease;
            font-weight: 500;
            color: white;
        }

        .footer-button.red {
            background: rgba(220, 53, 69, 0.85);
            border-left: 4px solid #dc3545;
        }

        .footer-button.green {
            background: rgba(25, 135, 84, 0.85);
            border-left: 4px solid #198754;
        }

        .footer-button:hover {
            transform: translateY(-2px);
        }

        .footer-button.red:hover {
            background: rgba(220, 53, 69, 0.95);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.25);
        }

        .footer-button.green:hover {
            background: rgba(25, 135, 84, 0.95);
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.25);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(150, 155, 231, 0.5);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(150, 155, 231, 0.8);
        }

        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        /* Original purple button */
        .modern-button.purple {
            background: linear-gradient(135deg, #969BE7, #8084d9);
        }

        /* New colorful button variations */
        .modern-button.blue {
            background: linear-gradient(135deg, #4F9CEF, #377DC8);
        }

        .modern-button.teal {
            background: linear-gradient(135deg, #4FCFBD, #35A796);
        }

        .modern-button.green {
            background: linear-gradient(135deg, #6DC97B, #4FA75A);
        }

        .modern-button.orange {
            background: linear-gradient(135deg, #FFA95C, #FF8A2B);
        }

        .modern-button.red {
            background: linear-gradient(135deg, #FF7070, #E55050);
        }

        .modern-button.pink {
            background: linear-gradient(135deg, #FF7AB4, #E0519A);
        }

        .modern-button.yellow {
            background: linear-gradient(135deg, #FFD159, #EAB72D);
        }

        /* Shimmer effect for all button colors */
        .glass-card:hover .modern-button::after {
            animation: shimmer 1.5s infinite;
        }

        /* Icon circles with custom colors */
        .icon-circle.purple {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(150,155,231,0.15));
        }

        .icon-circle.blue {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(79,156,239,0.15));
        }

        .icon-circle.teal {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(79,207,189,0.15));
        }

        .icon-circle.green {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(109,201,123,0.15));
        }

        .icon-circle.orange {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(255,169,92,0.15));
        }

        .icon-circle.red {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(255,112,112,0.15));
        }

        .icon-circle.pink {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(255,122,180,0.15));
        }

        .icon-circle.yellow {
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(255,209,89,0.15));
        }
    </style>
</head>
<body class="relative h-screen overflow-hidden bg-gradient-to-br from-white to-[#fcf8fb]">

    <!-- Circle Blur Background -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <!-- Lingkaran 1 -->
        <div class="absolute w-[300px] h-[300px] bg-[#D1D0EF] rounded-full opacity-90 blur-3xl top-20 right-20"></div>

        <!-- Lingkaran 2 -->
        <div class="absolute w-[250px] h-[250px] bg-[#EEC1DD] rounded-full opacity-70 blur-3xl bottom-40 left-10"></div>

        <!-- Lingkaran 3 -->
        <div class="absolute w-[300px] h-[300px] bg-[#969BE7] rounded-full opacity-60 blur-3xl top-40 right-1/4"></div>

        <!-- Additional circles in the middle -->
        <div class="absolute w-[350px] h-[350px] bg-[#D1D0EF] rounded-full opacity-50 blur-3xl top-1/3 left-1/3"></div>
        <div class="absolute w-[200px] h-[200px] bg-[#EEC1DD] rounded-full opacity-60 blur-3xl top-1/2 right-1/3"></div>
        <div class="absolute w-[280px] h-[280px] bg-[#969BE7] rounded-full opacity-40 blur-3xl bottom-1/4 left-1/4"></div>
        <div class="absolute w-[320px] h-[320px] bg-[#D1D0EF] rounded-full opacity-30 blur-3xl bottom-1/3 right-1/4"></div>
    </div>

    <div class="container mx-auto px-4 py-4 relative z-10 h-screen flex flex-col">
        <!-- Main Content -->
        <div class="max-w-6xl mx-auto w-full flex flex-col h-full">
            <!-- Header with location info -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-2 mb-8">
                <h1 class="text-xl md:text-2xl font-extrabold text-gray-800 text-shadow">Portal Layanan Desa</h1>
                <!-- Optionally show location info if you need it -->
                <!-- <p class="text-sm text-gray-600">Lokasi: {{ $village_id }}</p> -->
                <div class="relative w-full md:w-1/3">
                    <input type="text" placeholder="Cari Layanan..." class="w-full rounded-3xl py-1.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-custom-purple glass-card">
                    <div class="absolute left-3 top-2 text-gray-500">
                        <i class="fa fa-search"></i>
                    </div>
                </div>
            </div>

            <!-- Card Container with internal scroll -->
            <div class="flex-grow overflow-auto pb-1 custom-scrollbar pt-3">
                <!-- Card Flex Layout -->
                <div class="flex flex-wrap justify-center gap-x-3 gap-y-8">
                    <!-- Card 1: Blue -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle blue w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-file-lines fa-xl text-blue-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.administrasi', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button blue text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Administrasi Umum
                            </button>
                        </a>
                    </div>

                    <!-- Card 2: Red -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle red w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-people-group fa-xl text-red-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.keramaian', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button red text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Ijin Keramaian
                            </button>
                        </a>
                    </div>

                    {{-- <!-- Card 3: Orange - Surat Pengantar KTP -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle orange w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-id-card fa-xl text-orange-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.ktp', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button orange text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Pengantar KTP
                            </button>
                        </a>
                    </div> --}}

                    {{-- <!-- Card 4: Pink - Surat Keterangan Kelahiran -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle pink w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-baby fa-xl text-pink-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.kelahiran', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button pink text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Keterangan Kelahiran
                            </button>
                        </a>
                    </div> --}}

                    {{-- <!-- Card 5: Purple - Surat Keterangan Kematian -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle purple w-16 h-16 md:w-20 md:h-20">
                            <i class="fa-solid fa-house-medical fa-xl text-purple-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.kematian', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button purple text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Keterangan Kematian
                            </button>
                        </a>
                    </div> --}}

                    <!-- Card 6: Yellow -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle yellow w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-shield fa-xl text-yellow-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.skck', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button yellow text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Permohonan SKCK
                            </button>
                        </a>
                    </div>

                    <!-- Card 7: Green -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle green w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-house fa-xl text-green-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.rumah-sewa', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button green text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Ijin Rumah Sewa
                            </button>
                        </a>
                    </div>

                    <!-- Card 8: Teal -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle teal w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-magnifying-glass fa-xl text-teal-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.kehilangan', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button teal text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Kehilangan
                            </button>
                        </a>
                    </div>

                    <!-- Card 9: Blue -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle blue w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-location-dot fa-xl text-blue-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.domisili', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button blue text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Keterangan Domisili
                            </button>
                        </a>
                    </div>

                    <!-- Card 10: Orange -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle orange w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-users fa-xl text-orange-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.ahli-waris', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button orange text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Keterangan Ahli Waris
                            </button>
                        </a>
                    </div>

                    <!-- Card 11: Pink -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle pink w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-store fa-xl text-pink-600"></i>
                        </div>
                        <a href="{{ route('guest.surat.domisili-usaha', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button pink text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Surat Keterangan Domisili Usaha
                            </button>
                        </a>
                    </div>

                    <!-- Card 12: Purple -->
                    <div class="glass-card rounded-2xl p-3 text-center hover:shadow-lg transition duration-300 w-[150px] md:w-[170px]">
                        <div class="icon-circle purple w-16 h-16 md:w-20 md:h-20">
                            <i class="fa fa-book fa-xl text-purple-600"></i>
                        </div>
                        <a href="{{ route('guest.buku-tamu', [
                            'province_id' => $province_id ?? null,
                            'district_id' => $district_id ?? null,
                            'sub_district_id' => $sub_district_id ?? null,
                            'village_id' => $village_id ?? null
                        ]) }}" class="block">
                            <button class="modern-button purple text-white py-2 w-full rounded-3xl font-medium shadow-md text-sm">
                                Buku Tamu
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-between pt-1 mt-0">
                {{-- <a href="{{ route('guest.pelayanan.index') }}" class="footer-button red px-5 py-2 rounded-3xl shadow-md transition-all duration-300">
                    <i class="fa fa-arrow-left mr-2"></i> Kembali
                </a> --}}
                <button class="footer-button green px-5 py-2 rounded-3xl shadow-md transition-all duration-300" style="background: rgba(255, 193, 7, 0.85); border-left: 4px solid #ffc107;">
                    <i class="fa fa-headset mr-2"></i> Tanya Petugas
                </button>
            </div>
        </div>
    </div>
<script>
        // Function to show success alert without timer
        function showSuccessAlert(message) {
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: message,
                showConfirmButton: true
            });
        }

        // Function to show error alert without timer
        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: message,
                showConfirmButton: true
            });
        }

        // Function to show warning alert without timer
        function showWarningAlert(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: message,
                showConfirmButton: true
            });
        }

        // Function to show info alert without timer
        function showInfoAlert(message) {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: message,
                showConfirmButton: true
            });
        }

        // Function to show confirmation dialog
        function showConfirmDialog(title, message, confirmCallback, cancelCallback) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed && typeof confirmCallback === 'function') {
                    confirmCallback();
                } else if (result.dismiss === Swal.DismissReason.cancel && typeof cancelCallback === 'function') {
                    cancelCallback();
                }
            });
        }

        // Check for flash messages and display alerts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSuccessAlert("{{ session('success') }}");
            @endif

            @if(session('error'))
                showErrorAlert("{{ session('error') }}");
            @endif

            @if(session('warning'))
                showWarningAlert("{{ session('warning') }}");
            @endif

            @if(session('info'))
                showInfoAlert("{{ session('info') }}");
            @endif
        });
    </script>

</body>
</html>

