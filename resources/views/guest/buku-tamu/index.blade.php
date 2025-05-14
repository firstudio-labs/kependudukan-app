<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Tamu - Portal Layanan Desa</title>
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

    <!-- Signature pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

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

        .glass-form {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        .text-shadow {
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
            margin: 0;
            padding: 0;
        }

        #signature-pad {
            border: 1px dashed #969BE7;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.8);
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
        <div class="max-w-4xl mx-auto w-full flex flex-col h-full">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-2 mb-6">
                <h1 class="text-xl md:text-2xl font-extrabold text-gray-800 text-shadow">Buku Tamu</h1>
            </div>

            <!-- Form Container with internal scroll -->
            <div class="flex-grow overflow-auto pb-1 custom-scrollbar pt-3">
                <div class="glass-form">
                    <form action="{{ route('guest.buku-tamu.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="province_id" value="{{ $province_id }}">
                        <input type="hidden" name="district_id" value="{{ $district_id }}">
                        <input type="hidden" name="sub_district_id" value="{{ $sub_district_id }}">
                        <input type="hidden" name="village_id" value="{{ $village_id }}">
                        <input type="hidden" name="tanda_tangan" id="tanda_tangan_value">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div class="space-y-2">
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama" id="nama" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-custom-purple focus:border-custom-purple" required>
                            </div>

                            <!-- No Telepon -->
                            <div class="space-y-2">
                                <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon <span class="text-red-500">*</span></label>
                                <input type="text" name="no_telepon" id="no_telepon" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-custom-purple focus:border-custom-purple" required>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-custom-purple focus:border-custom-purple">
                            </div>

                            <!-- Keperluan -->
                            <div class="space-y-2">
                                <label for="keperluan" class="block text-sm font-medium text-gray-700">Keperluan <span class="text-red-500">*</span></label>
                                <input type="text" name="keperluan" id="keperluan" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-custom-purple focus:border-custom-purple" required>
                            </div>

                            <!-- Alamat - full width -->
                            <div class="space-y-2 md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                                <textarea name="alamat" id="alamat" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-custom-purple focus:border-custom-purple" required></textarea>
                            </div>

                            <!-- Pesan - full width -->
                            <div class="space-y-2 md:col-span-2">
                                <label for="pesan" class="block text-sm font-medium text-gray-700">Pesan/Kesan</label>
                                <textarea name="pesan" id="pesan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-custom-purple focus:border-custom-purple"></textarea>
                            </div>

                            <!-- Tanda Tangan - full width -->
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Tanda Tangan</label>
                                <div class="flex flex-col items-center space-y-3">
                                    <canvas id="signature-pad" class="w-full h-48"></canvas>
                                    <div class="flex space-x-2">
                                        <button type="button" id="clear-signature" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-gray-700">
                                            <i class="fa fa-eraser mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="w-full py-3 bg-custom-purple hover:bg-custom-purple-hover text-white font-semibold rounded-lg shadow-md transition duration-300">
                                <i class="fa fa-check-circle mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-between pt-4 mt-0">
                <a href="{{ route('guest.pelayanan.list', ['province_id' => $province_id, 'district_id' => $district_id, 'sub_district_id' => $sub_district_id, 'village_id' => $village_id]) }}" class="footer-button red px-5 py-2 rounded-3xl shadow-md transition-all duration-300">
                    <i class="fa fa-arrow-left mr-2"></i> Kembali
                </a>
                <button class="footer-button green px-5 py-2 rounded-3xl shadow-md transition-all duration-300" style="background: rgba(255, 193, 7, 0.85); border-left: 4px solid #ffc107;">
                    <i class="fa fa-headset mr-2"></i> Tanya Petugas
                </button>
            </div>
        </div>
    </div>

    <script>
        // Signature Pad
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize signature pad
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            // Adjust canvas size
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear(); // Clear canvas after resize
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas(); // Set canvas size on page load

            // Clear button
            document.getElementById('clear-signature').addEventListener('click', function() {
                signaturePad.clear();
            });

            // On form submit, save signature data to hidden input
            document.querySelector('form').addEventListener('submit', function(e) {
                if (!signaturePad.isEmpty()) {
                    const signatureData = signaturePad.toDataURL();
                    document.getElementById('tanda_tangan_value').value = signatureData;
                }
            });
        });

        // Function to show success alert
        function showSuccessAlert(message) {
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: message,
                showConfirmButton: true
            });
        }

        // Function to show error alert
        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: message,
                showConfirmButton: true
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
        });
    </script>
</body>
</html> 