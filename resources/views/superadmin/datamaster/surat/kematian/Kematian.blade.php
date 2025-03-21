<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kematian - {{ $kematian->full_name }}</title>
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
                <p class="text-sm">Alamat: </p>
            </div>
            <div class="w-24">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN KEMATIAN</h1>
            <p class="text-sm">Nomor: {{ $kematian->letter_number ?? '-' }}</p>
        </div>

        <div class="content">
            <p>Yang bertanda tangan di bawah ini, Lurah/Kepala Desa {{ $villageName }} Kecamatan {{ $subdistrictName }} Kabupaten {{ $districtName }}, dengan ini menerangkan bahwa:</p>
        </div>

        <div class="mb-6 mt-4">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td width="30%">Nama Lengkap</td>
                        <td width="5%">:</td>
                        <td>{{ $kematian->full_name }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $kematian->nik }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $kematian->birth_place }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($kematian->birth_date)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $genderName }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $religionName }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $kematian->address }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <p class="mb-2">Berdasarkan {{ $kematian->info }} dari Ketua RT {{ $kematian->rt ?? '-' }} Desa/Kelurahan {{ $villageName }}, Kecamatan {{ $subdistrictName }},
            @if($kematian->rt_letter_date)
               Tanggal {{ \Carbon\Carbon::parse($kematian->rt_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
            @endif
            bahwa benar yang bersangkutan saat ini telah meninggal dunia:</p>
        </div>

        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td width="30%">Tanggal Kematian</td>
                        <td width="5%">:</td>
                        <td>{{ \Carbon\Carbon::parse($kematian->death_date)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    </tr>
                    <tr>
                        <td>Penyebab Kematian</td>
                        <td>:</td>
                        <td>{{ $kematian->death_cause }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Kematian</td>
                        <td>:</td>
                        <td>{{ $kematian->death_place }}</td>
                    </tr>
                    <tr>
                        <td>Nama Pelapor</td>
                        <td>:</td>
                        <td>{{ $kematian->reporter_name }}</td>
                    </tr>
                    <tr>
                        <td>Hubungan Pelapor dengan yang meninggal</td>
                        <td>:</td>
                        <td>{{ $kematian->reporter_relation }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Signature -->
        <div class="text-right mt-16">
            <div class="mb-4">
                {{ $villageName }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </div>
            <p class="font-bold">KEPALA DESA {{ strtoupper($villageName) }}</p>
            <div class="mt-20">
                <!-- Space for signature -->
                <p class="font-bold underline">{{ strtoupper($kematian->signing ?? 'KEPALA DESA') }}</p>
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
