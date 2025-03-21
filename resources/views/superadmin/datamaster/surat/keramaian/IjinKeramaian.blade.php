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
                <div class="w-20 h-20 bg-gray-200 flex items-center justify-center">Logo</div>
            </div>
            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH KABUPATEN {{ strtoupper($districtName) }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrictName) }}</p>
                <p class="text-2xl font-bold">KELURAHAN {{ strtoupper($villageName) }}</p>
                <p class="text-sm">Alamat: Jalan Desa {{ $villageName }} Kecamatan {{ $subdistrictName }} Kabupaten {{ $districtName }}</p>
            </div>
            <div class="w-24">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold underline">SURAT IZIN KERAMAIAN</h1>
            <p class="text-sm">Nomor: {{ $keramaian->letter_number ?? '-' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p>Yang bertanda tangan di bawah ini, Kepala Desa/Lurah {{ $villageName }} Kecamatan {{ $subdistrictName }} Kabupaten {{ $districtName }}, dengan ini menerangkan bahwa:</p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Pemohon</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $keramaian->full_name }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $keramaian->nik }}</td>
                    </tr>
                    <tr>
                        <td>Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $keramaian->birth_place }},
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
                        <td>{{ $keramaian->address }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <p>Bahwa yang bersangkutan bermaksud untuk mengadakan {{ $keramaian->event }} dengan keterangan sebagai berikut:</p>
        </div>

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
                        <td>Tempat</td>
                        <td>:</td>
                        <td>{{ $keramaian->place }}</td>
                    </tr>
                    <tr>
                        <td>Hiburan</td>
                        <td>:</td>
                        <td>{{ $keramaian->entertainment }}</td>
                    </tr>
                    <tr>
                        <td>Acara</td>
                        <td>:</td>
                        <td>{{ $keramaian->event }}</td>
                    </tr>
                    <tr>
                        <td>Undangan</td>
                        <td>:</td>
                        <td>{{ $keramaian->invitation }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <p>Demikian surat izin ini diberikan kepada yang bersangkutan untuk dapat dipergunakan sebagaimana mestinya dan kepada pihak yang terkait dimohon bantuan seperlunya.</p>
        </div>

        <!-- Signature -->
        <div class="text-right mt-16">
            <div class="mb-4">
                {{ $villageName }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </div>
            <p class="font-bold">KEPALA DESA {{ strtoupper($villageName) }}</p>
            <div class="mt-20">
                <!-- Space for signature -->
                <p class="font-bold underline">{{ strtoupper($keramaian->signing ?? 'KEPALA DESA') }}</p>
                <p>NIP. __________________</p>
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
