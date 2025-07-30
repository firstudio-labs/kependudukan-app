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
            <div class="w-32 h-32 mr-4 flex items-center justify-center">
                @if(isset($district_logo) && !empty($district_logo))
                    <img src="{{ asset('storage/' . $district_logo) }}" alt="Logo Kabupaten" class="max-w-[120px] max-h-[120px] object-contain">
                @else
                    <!-- Fallback to default logo -->
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Default" class="max-w-[120px] max-h-[120px] object-contain">
                @endif
            </div>
            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($districtName) }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrictName) }}</p>
                <p class="text-2xl font-bold">
                    @if(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '1')
                        KELURAHAN
                    @elseif(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '2')
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
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN KEMATIAN</h1>
            <p class="text-sm">Nomor: {{ $kematian->letter_number ?? '-' }}</p>
        </div>

        <div class="content">
            <p>
                @if(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '1')
                    Lurah
                @elseif(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '2')
                    Kepala Desa
                @else
                    {{ isset($administrationData) && isset($administrationData['village_head_title']) ? $administrationData['village_head_title'] : 'Lurah/Kepala Desa' }}
                @endif
                {{ $villageName }} Kecamatan {{ $subdistrictName }}, dengan ini menerangkan bahwa:
            </p>
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
                        <td>
                            {{ $kematian->address ?? '-' }}
                            RT {{ $kematian->rt ?? '0' }},
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
            <p class="mb-2">
                Berdasarkan {{ $kematian->info ?? 'Surat Keterangan' }} dari Ketua RT {{ $kematian->rt ?? 'XX' }}
                {{ $kematian->address ?? '-' }},
                {{ $villageName ?? 'XXXX' }},
                {{ $subdistrictName ?? 'XXXX' }},
                {{ $districtName ?? 'XXXX' }},
                {{ $provinceName ?? 'XXXX' }},
                Tanggal
                @if(isset($kematian->rt_letter_date) && !empty($kematian->rt_letter_date))
                    {{ \Carbon\Carbon::parse($kematian->rt_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    XX-XX-XXXX
                @endif
                bahwa:
            </p>
            <p class="mb-4">benar yang bersangkutan saat ini telah meninggal dunia.</p>
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
                        <td>
                            @php
                                // Map relationship ID to human-readable name
                                $relationships = [
                                    1 => 'Suami',
                                    2 => 'Istri',
                                    3 => 'Anak',
                                    4 => 'Orang Tua',
                                    5 => 'Saudara',
                                    6 => 'Kerabat',
                                    7 => 'Tetangga',
                                    8 => 'Lainnya'
                                ];

                                // Get relationship name or show original value if not found in the map
                                $relationshipName = $relationships[$kematian->reporter_relation] ?? $kematian->reporter_relation;
                            @endphp
                            {{ $relationshipName }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $villageName }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
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
