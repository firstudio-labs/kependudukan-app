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
                <p class="text-lg font-bold">PEMERINTAH KABUPATEN {{ strtoupper($district_name) }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrict_name) }}</p>
                <p class="text-2xl font-bold">KELURAHAN {{ strtoupper($village_name) }}</p>
                <p class="text-sm">Alamat:</p>

            </div>
            <div class="w-24">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold">SURAT KETERANGAN AHLI WARIS</h1>
            <p class="text-sm">Nomor: {{ $ahliWaris->letter_number ?? '-' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">Kepala Desa/Lurah {{ $village_name }} Kecamatan {{ $subdistrict_name }} dengan ini menerangkan bahwa :</p>
        </div>

        <!-- Deceased Person Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td>{{ isset($heirs[0]['full_name']) && is_string($heirs[0]['full_name']) ? $heirs[0]['full_name'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ is_array($ahliWaris->nik) ? (isset($ahliWaris->nik[0]) ? $ahliWaris->nik[0] : '-') : (is_string($ahliWaris->nik) ? $ahliWaris->nik : '-') }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ is_string($ahliWaris->death_place) ? $ahliWaris->death_place : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ is_string($formatted_death_date) ? $formatted_death_date : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ isset($heirs[0]['gender']) && is_string($heirs[0]['gender']) ? $heirs[0]['gender'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ isset($heirs[0]['religion']) && is_string($heirs[0]['religion']) ? $heirs[0]['religion'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ is_string($ahliWaris->address ?? '') ? $ahliWaris->address : (isset($heirs[0]['address']) && is_string($heirs[0]['address']) ? $heirs[0]['address'] : '-') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Heir Information (Second Person) -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td>{{ isset($heirs[1]['full_name']) && is_string($heirs[1]['full_name']) ? $heirs[1]['full_name'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ isset($heirs[1]['nik']) && is_string($heirs[1]['nik']) ? $heirs[1]['nik'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ isset($heirs[1]['birth_place']) && is_string($heirs[1]['birth_place']) ? $heirs[1]['birth_place'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ isset($heirs[1]['birth_date']) && is_string($heirs[1]['birth_date']) ? $heirs[1]['birth_date'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ isset($heirs[1]['gender']) && is_string($heirs[1]['gender']) ? $heirs[1]['gender'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ isset($heirs[1]['religion']) && is_string($heirs[1]['religion']) ? $heirs[1]['religion'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ isset($heirs[1]['address']) && is_string($heirs[1]['address']) ? $heirs[1]['address'] : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $village_name }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </div>
            <p class="font-bold">KEPALA DESA</p>
            <div class="mt-20">
                <!-- Space for signature -->
                <p class="font-bold underline">{{ strtoupper($ahliWaris->signing ?? '(NAMA KEPALA DESA)') }}</p>
                <span class="bg-yellow-200 px-2 py-1">NIP. __________________</span>
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
