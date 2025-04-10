<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portal Layanan Desa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(255,255,255,0.2), rgba(150,155,231,0.1));
            box-shadow: 0 8px 16px rgba(31, 38, 135, 0.1);
            margin: 0 auto 1.25rem auto;
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
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden bg-gradient-to-br from-white to-[#fcf8fb]">

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

    <div class="container mx-auto px-4 py-6 relative z-10">
        <!-- Main Content -->
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
                <h1 class="text-2xl font-extrabold text-gray-800 text-shadow">Portal Layanan Desa</h1>
                <div class="relative w-full md:w-1/3">
                    <input type="text" placeholder="Cari Layanan..." class="w-full rounded-3xl py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-custom-purple glass-card">
                    <div class="absolute left-3 top-2.5 text-gray-500">
                        <i class="fa fa-search"></i>
                    </div>
                </div>
            </div>

            <!-- Card Flex Layout -->
            <div class="flex flex-wrap justify-center gap-5">
                <!-- Card Template -->
                <template id="card-template">
                    <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300">
                        <div class="icon-circle">
                            <i class="fa fa-file-lines fa-2x text-gray-600"></i>
                        </div>
                        <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                            Nama Layanan
                        </button>
                    </div>
                </template>

                <!-- Rendered Cards with varying widths -->
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-64">
                    <div class="icon-circle">
                        <i class="fa fa-file-lines fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Administrasi Umum
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-72">
                    <div class="icon-circle">
                        <i class="fa fa-people-group fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Ijin Keramaian
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-60">
                    <div class="icon-circle">
                        <i class="fa fa-id-card fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Pengantar KTP
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-80">
                    <div class="icon-circle">
                        <i class="fa fa-baby fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Keterangan Kelahiran
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-72">
                    <div class="icon-circle">
                        <i class="fa fa-cross fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Keterangan Kematian
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-64">
                    <div class="icon-circle">
                        <i class="fa fa-shield fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Permohonan SKCK
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-72">
                    <div class="icon-circle">
                        <i class="fa fa-house fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Ijin Rumah Sewa
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-60">
                    <div class="icon-circle">
                        <i class="fa fa-magnifying-glass fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Kehilangan
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-80">
                    <div class="icon-circle">
                        <i class="fa fa-location-dot fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Keterangan Domisili
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-72">
                    <div class="icon-circle">
                        <i class="fa fa-users fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Keterangan Ahli Waris
                    </button>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center hover:shadow-lg transition duration-300 w-full sm:w-5/12 md:w-80">
                    <div class="icon-circle">
                        <i class="fa fa-store fa-2x text-gray-600"></i>
                    </div>
                    <button class="modern-button text-white py-2.5 w-full rounded-3xl font-medium shadow-md">
                        Surat Keterangan Domisili Usaha
                    </button>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-between pt-8">
                <a href="{{ route('guest.pelayanan.index') }}" class="footer-button red px-7 py-2.5 rounded-3xl shadow-md transition-all duration-300">
                    <i class="fa fa-arrow-left mr-2"></i> Kembali
                </a>
                <button class="footer-button green px-7 py-2.5 rounded-3xl shadow-md transition-all duration-300">
                    <i class="fa fa-headset mr-2"></i> Tanya Petugas
                </button>
            </div>
        </div>
    </div>

</body>
</html>
