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
                <!-- Replace placeholder with existing logo from your public folder -->
                <img src="{{ asset('images/logo.png') }}" alt="Logo Kota" class="w-full h-auto">
                <!-- If you don't have a logo yet, use this empty div instead of img tag -->
                <!-- <div class="w-24 h-24"></div> -->
            </div>
            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($district_name ?? 'XXXX') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrict_name ?? 'XXXX') }}</p>
                <p class="text-2xl font-bold">
                    @if(isset($village_code) && strlen($village_code) >= 7 && substr($village_code, 6, 1) === '1')
                        KELURAHAN
                    @elseif(isset($village_code) && strlen($village_code) >= 7 && substr($village_code, 6, 1) === '2')
                        DESA
                    @else
                        {{ isset($administrationData) && isset($administrationData['village_type']) ? strtoupper($administrationData['village_type']) : 'DESA/KELURAHAN' }}
                    @endif
                    {{ strtoupper($village_name ?? 'XXXX') }}
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
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN</h1>
            <p class="text-sm">Nomor : {{ $administration->letter_number ?? '...' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">
                @if(isset($village_code) && strlen($village_code) >= 7 && substr($village_code, 6, 1) === '1')
                    Lurah
                @elseif(isset($village_code) && strlen($village_code) >= 7 && substr($village_code, 6, 1) === '2')
                    Kepala Desa
                @else
                    {{ isset($administrationData) && isset($administrationData['village_head_title']) ? $administrationData['village_head_title'] : 'Lurah/Kepala Desa' }}
                @endif
                {{ $village_name ?? 'XXXX' }} Kecamatan {{ $subdistrict_name ?? 'XXXX' }} dengan ini menerangkan bahwa :
            </p>
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
                            @if(isset($formatted_birth_date) && strpos($formatted_birth_date, '-') !== false)
                                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $formatted_birth_date)->locale('id')->isoFormat('D MMMM Y') }}
                            @else
                                {{ $formatted_birth_date ?? 'XXXX' }}
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
                        <td>{{ $job_name ?? 'XXXX' }}</td>
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
                        <td>
                            {{ $administration->address ?? '-' }}
                            RT {{ $administration->rt ?? '0' }},
                            {{ !empty($village_name) ? $village_name : 'Desa/Kelurahan' }},
                            {{ !empty($subdistrict_name) ? $subdistrict_name : 'Kecamatan' }},
                            {{ !empty($district_name) ? $district_name : 'Kabupaten' }},
                            {{ !empty($province_name) ? $province_name : 'Provinsi' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Statement -->
        <div class="mb-6">
            <p class="mb-2">
                Berdasarkan Surat Keterangan dari Ketua RT {{ $administration->rt ?? 'XX' }}
                {{ $administration->address ?? '-' }},
                {{ $village_name ?? 'XXXX' }},
                {{ $subdistrict_name ?? 'XXXX' }},
                {{ $district_name ?? 'XXXX' }},
                {{ $province_name ?? 'XXXX' }},
                Tanggal
                @if(isset($formatted_letter_date) && !empty($formatted_letter_date))
                    {{ \Carbon\Carbon::parse($formatted_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    XX-XX-XXXX
                @endif
                bahwa:
            </p>
            <p class="mb-4">{{ $administration->statement_content ?? 'XXXX' }}</p>
            <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan {{ $administration->purpose ?? 'XXXX' }}</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $village_name ?? 'XXXX' }},
                @if(isset($formatted_letter_date) && !empty($formatted_letter_date))
                    {{ \Carbon\Carbon::parse($formatted_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                @endif
            </div>
            <p>{{ strtoupper($signing_name ?? 'NAMA KEPALA DESA') }}</p>
            <div class="mt-20">
                <div class="border-b border-black inline-block w-48"></div>
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
