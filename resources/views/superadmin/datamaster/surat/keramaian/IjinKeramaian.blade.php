<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Keramaian - {{ $keramaian->full_name }}</title>
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
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($districtName) }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrictName) }}</p>
                <p class="text-2xl font-bold">
                    @if(isset($villageCode) && substr($villageCode, 0, 1) === '1')
                        KELURAHAN
                    @elseif(isset($villageCode) && substr($villageCode, 0, 1) === '2')
                        DESA
                    @else
                        {{ isset($administrationData) && isset($administrationData['village_type']) ? strtoupper($administrationData['village_type']) : 'DESA/KELURAHAN' }}
                    @endif
                    {{ strtoupper($villageName ?? 'XXXX') }}
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
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN IJIN KERAMAIAN</h1>
            <p class="text-sm">Nomor: {{ $keramaian->letter_number ?? '-' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">Kepala Desa/Lurah {{ $villageName }} Kecamatan {{ $subdistrictName }}  dengan ini menerangkan bahwa :</p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $keramaian->full_name }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $keramaian->nik }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $keramaian->birth_place }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $birthDate }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $genderName }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $jobName }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $religionName }}</td>
                    </tr>
                    <tr>
                        <td>Kewarganegaraan</td>
                        <td>:</td>
                        <td>{{ $citizenStatusName }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>
                            {{ $keramaian->address ?? '-' }}
                            RT {{ $keramaian->rt ?? '0' }},
                            {{ !empty($villageName) ? $villageName : 'Desa/Kelurahan' }},
                            {{ !empty($subdistrictName) ? $subdistrictName : 'Kecamatan' }},
                            {{ !empty($districtName) ? $districtName : 'Kabupaten' }},
                            {{ !empty($provinceName) ? $provinceName : 'Provinsi' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6">

            <p class="mb-4">yang bersangkutan diatas mengajukan permohonan SURAT IZIN KERAMAIAN yang akan dilaksanakan pada:</p>
        </div>

        <!-- Event Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Hari</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $keramaian->day }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ $eventDate }}</td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($keramaian->time)->format('H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td>Undangan</td>
                        <td>:</td>
                        <td>{{ $keramaian->invitation }}</td>
                    </tr>
                    <tr>
                        <td>Acara</td>
                        <td>:</td>
                        <td>{{ $keramaian->event }}</td>
                    </tr>
                    <tr>
                        <td>Hiburan</td>
                        <td>:</td>
                        <td>{{ $keramaian->entertainment }}</td>
                    </tr>
                    <tr>
                        <td>Tempat</td>
                        <td>:</td>
                        <td>{{ $keramaian->place }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $villageName }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </div>
            <p class="font-bold">
                <p class="font-bold underline">{{ strtoupper($signing_name ?? 'NAMA KEPALA DESA') }}</p>
            </p>
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
