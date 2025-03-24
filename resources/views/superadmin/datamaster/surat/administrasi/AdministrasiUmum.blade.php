<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap');

        body {
            font-family: 'Times New Roman', Times, serif;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-white p-8">
    <div class="max-w-4xl mx-auto bg-white p-8">
        <!-- Print Button - Only visible on screen -->
        <div class="no-print mb-4 flex justify-end">
            <button onclick="window.print()" class="bg-[#2D336B] text-white px-4 py-2 rounded hover:bg-[#5C69A7]">
                <i class="fa-solid fa-print mr-2"></i> Cetak Dokumen
            </button>
        </div>

        <div class="flex items-center mb-4">
            <div class="w-24 mr-4">
                <img src="/api/placeholder/100/100" alt="Logo Kota" class="w-full h-auto">
            </div>
            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($districtName ?? 'XXXX') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrictName ?? 'XXXX') }}</p>
                <p class="text-2xl font-bold">KELURAHAN {{ strtoupper($villageName ?? 'XXXX') }}</p>
                <p class="text-sm">Alamat: {{ $administration->address ?? 'XXXX' }}</p>
            </div>
            <div class="w-24">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN</h1>
            <p class="text-sm">Nomor : {{ $administration->letter_number ?? '...' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">Lurah {{ $villageName ?? 'XXXX' }} Kecamatan {{ $subdistrictName ?? 'XXXX' }} dengan ini menerangkan bahwa :</p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $administration->full_name ?? 'XXXX' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $administration->nik ?? 'XXXX' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $administration->birth_place ?? 'XXXX' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            @if(isset($birthDate) && strpos($birthDate, '-') !== false)
                                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $birthDate)->locale('id')->isoFormat('D MMMM Y') }}
                            @else
                                {{ $birthDate ?? 'XXXX' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $gender ?? 'XXXX' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $jobName ?? 'XXXX' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $religion ?? 'XXXX' }}</td>
                    </tr>
                    <tr>
                        <td>Kewarganegaraan</td>
                        <td>:</td>
                        <td>{{ $citizenship ?? 'XXXX' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $administration->address ?? 'XXXX' }} RT. {{ $administration->rt ?? 'XX' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Statement -->
        <div class="mb-6">
            <p class="mb-2">Berdasarkan Surat Keterangan dari Ketua RT {{ $administration->rt ?? 'XX' }} Desa/Kelurahan {{ $villageName ?? 'XXXX' }}, Kecamatan {{ $subdistrictName ?? 'XXXX' }}, Tanggal {{ $letterDate ?? 'XX-XX-XXXX' }} bahwa</p>
            <p class="mb-4">{{ $administration->statement_content ?? 'XXXX' }}</p>
            <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan {{ $administration->purpose ?? 'XXXX' }}</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $villageName ?? 'XXXX' }},
                @if(isset($letterDate) && strpos($letterDate, '-') !== false)
                    {{ \Carbon\Carbon::createFromFormat('d-m-Y', $letterDate)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    {{ $letterDate ?? \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                @endif
            </div>
            <p class="font-bold">KEPALA DESA {{ strtoupper($villageName ?? 'XXXX') }}</p>
            <div class="mt-20">
                <!-- Space for signature -->
                <p class="font-bold underline">{{ strtoupper($administration->signing ?? 'NAMA KEPALA DESA') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-print when the page loads (optional)
        window.onload = function() {
            // Uncomment this line if you want the print dialog to appear automatically
            // window.print();
        };
    </script>
</body>

</html>
