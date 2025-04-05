<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kehilangan</title>
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
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($districtName ?? 'TARAKAN') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrictName ?? 'TARAKAN BARAT') }}</p>
                <p class="text-2xl font-bold">
                    @if(isset($villageCode) && substr($villageCode, 0, 1) === '1')
                        KELURAHAN
                    @elseif(isset($villageCode) && substr($villageCode, 0, 1) === '2')
                        DESA
                    @else
                        {{ isset($administrationData) && isset($administrationData['village_type']) ? strtoupper($administrationData['village_type']) : 'DESA/KELURAHAN' }}
                    @endif
                    {{ strtoupper($villageName ?? 'KARANG ANYAR') }}
                </p>
                <p class="text-sm">Alamat: </p>
            </div>
            <div class="w-24">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN KEHILANGAN</h1>
            <p class="text-sm">Nomor : {{ $kehilangan->letter_number ?? '...' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">Lurah {{ $villageName ?? 'Karang Anyar' }} Kecamatan {{ $subdistrictName ?? 'Tarakan Barat' }} dengan ini menerangkan bahwa :</p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $kehilangan->full_name }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $kehilangan->nik }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $kehilangan->birth_place }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            @if(isset($birthDate) && strpos($birthDate, '-') !== false)
                                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $birthDate)->locale('id')->isoFormat('D MMMM Y') }}
                            @else
                                {{ $birthDate }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $gender }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $jobName }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $religion }}</td>
                    </tr>
                    <tr>
                        <td>Kewarganegaraan</td>
                        <td>:</td>
                        <td>{{ $citizenship }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>
                            {{ $kehilangan->address ?? '-' }}
                            RT {{ $kehilangan->rt ?? '0' }},
                            {{ !empty($villageName) ? $villageName : 'Desa/Kelurahan' }},
                            {{ !empty($subdistrictName) ? $subdistrictName : 'Kecamatan' }},
                            {{ !empty($districtName) ? $districtName : 'Kabupaten' }},
                            {{ !empty($provinceName) ? $provinceName : 'Provinsi' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Statement -->
        <div class="mb-6">
            <p class="mb-2">
                Berdasarkan Surat Keterangan dari Ketua RT {{ $kehilangan->rt ?? 'XX' }}
                @if(isset($villageCode) && substr($villageCode, 0, 1) === '1')
                    Kelurahan
                @elseif(isset($villageCode) && substr($villageCode, 0, 1) === '2')
                    Desa
                @else
                    Desa/Kelurahan
                @endif
                {{ $villageName ?? 'XXXX' }}, Kecamatan {{ $subdistrictName ?? 'XXXX' }},
                Tanggal
                @if(isset($letterDate) && !empty($letterDate))
                    {{ \Carbon\Carbon::parse($letterDate)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    XX-XX-XXXX
                @endif
                dan menurut keterangan yang bersangkutan telah kehilangan
                {{ $kehilangan->lost_items ?? 'XXXX' }}

            </p>
            <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $villageName ?? 'Karang Anyar' }},
                @if(isset($letterDate) && strpos($letterDate, '-') !== false)
                    {{ \Carbon\Carbon::createFromFormat('d-m-Y', $letterDate)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    {{ $letterDate ?? \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                @endif
            </div>
            <p class="font-bold">
                <p class="font-bold underline">{{ strtoupper($signing_name ?? 'NAMA KEPALA DESA') }}</p>
            </p>
        </div>
    </div>

    <script>
        // Auto-print when the page loads (optional)
        window.onload = function() {
            // Format dates in Indonesian
            const formatIndonesianDate = (dateString) => {
                if (!dateString) return '';

                // Check if it's already in the correct format
                if (dateString.includes(' ') && !dateString.includes('-')) return dateString;

                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                // Handle different formats
                let date;
                if (dateString.includes('-')) {
                    // Format: DD-MM-YYYY
                    const parts = dateString.split('-');
                    if (parts.length === 3) {
                        return parts[0] + ' ' + months[parseInt(parts[1])-1] + ' ' + parts[2];
                    }
                    date = new Date(dateString);
                } else {
                    date = new Date(dateString);
                }

                return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
            };

            // Convert all dates with format-date class
            document.querySelectorAll('.format-date').forEach(el => {
                el.textContent = formatIndonesianDate(el.textContent);
            });

            // Also convert directly in the document where dates might be hardcoded
            const dateElements = document.querySelectorAll('[data-date]');
            dateElements.forEach(el => {
                if (el.dataset.date) {
                    el.textContent = formatIndonesianDate(el.dataset.date);
                }
            });
        };
    </script>
</body>

</html>
