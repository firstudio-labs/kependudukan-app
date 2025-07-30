<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Ahli Waris</title>
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
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($district_name ?? $districtName ?? 'KABUPATEN') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrict_name ?? $subdistrictName ?? 'KECAMATAN') }}</p>
                <p class="text-2xl font-bold">
                    @if(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '1')
                        KELURAHAN
                    @elseif(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '2')
                        DESA
                    @else
                        {{ isset($administrationData) && isset($administrationData['village_type']) ? strtoupper($administrationData['village_type']) : 'DESA/KELURAHAN' }}
                    @endif
                    {{ strtoupper($village_name ?? $villageName ?? 'XXXX') }}
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
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN AHLI WARIS</h1>
            <p class="text-sm">Nomor: {{ $ahliWaris->letter_number ?? '___________' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">
                @if(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '1')
                    Lurah
                @elseif(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '2')
                    Kepala Desa
                @else
                    {{ isset($administrationData) && isset($administrationData['village_head_title']) ? $administrationData['village_head_title'] : 'Kepala Desa' }}
                @endif
                {{ $village_name ?? $villageName ?? 'Desa/Kelurahan' }} Kecamatan {{ $subdistrict_name ?? $subdistrictName ?? 'Kecamatan' }} dengan ini menerangkan bahwa :
            </p>
        </div>

        <!-- Heirs Information Section -->
        @php
            // Ensure heirs is an array
            $heirsList = [];
            if (isset($ahliWaris) && !empty($ahliWaris->nik) && is_array($ahliWaris->nik)) {
                // If the data is directly in the model
                for ($i = 0; $i < count($ahliWaris->nik); $i++) {
                    if (isset($ahliWaris->nik[$i]) && !empty($ahliWaris->nik[$i])) {
                        $heirsList[] = [
                            'nik' => $ahliWaris->nik[$i] ?? '-',
                            'full_name' => $ahliWaris->full_name[$i] ?? '-',
                            'birth_place' => $ahliWaris->birth_place[$i] ?? '-',
                            'birth_date' => $ahliWaris->birth_date[$i] ?? '-',
                            'gender' => $ahliWaris->gender[$i] ?? '-',
                            'religion' => $ahliWaris->religion[$i] ?? '-',
                            'address' => $ahliWaris->address[$i] ?? '-',
                            'family_status' => $ahliWaris->family_status[$i] ?? '-',
                        ];
                    }
                }
            } elseif (isset($heirs) && is_array($heirs)) {
                // If the data is in the heirs array
                $heirsList = $heirs;
            }
        @endphp

        <!-- Display all heirs information -->
        @foreach($heirsList as $index => $heir)
            <div class="mb-6">
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="w-1/3">Nama Lengkap</td>
                            <td class="w-1/12">:</td>
                            <td>{{ $heir['full_name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>:</td>
                            <td>{{ $heir['nik'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tempat Lahir</td>
                            <td>:</td>
                            <td>{{ $heir['birth_place'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td>:</td>
                            <td>
                                @if(isset($heir['birth_date']) && !empty($heir['birth_date']))
                                    @if(is_string($heir['birth_date']) && strpos($heir['birth_date'], '-') !== false)
                                        {{ \Carbon\Carbon::parse($heir['birth_date'])->locale('id')->isoFormat('D MMMM Y') }}
                                    @else
                                        {{ $heir['birth_date'] }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td>
                                @if(isset($heir['gender']))
                                    @if($heir['gender'] == 1)
                                        Laki-Laki
                                    @elseif($heir['gender'] == 2)
                                        Perempuan
                                    @else
                                        {{ $heir['gender'] }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>:</td>
                            <td>
                                @php
                                    $religions = [
                                        1 => 'Islam',
                                        2 => 'Kristen',
                                        3 => 'Katholik',
                                        4 => 'Hindu',
                                        5 => 'Buddha',
                                        6 => 'Kong Hu Cu',
                                        7 => 'Lainnya'
                                    ];
                                @endphp
                                @if(isset($heir['religion']) && isset($religions[$heir['religion']]))
                                    {{ $religions[$heir['religion']] }}
                                @else
                                    {{ $heir['religion'] ?? '-' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td>
                                {{ is_array($heir['address'] ?? null) ? implode(', ', $heir['address']) : ($heir['address'] ?? '-') }}
                                ,
                                {{ $village_name ?? $villageName ?? 'Desa/Kelurahan' }},
                                {{ $subdistrict_name ?? $subdistrictName ?? 'Kecamatan' }},
                                {{ $district_name ?? $districtName ?? 'Kabupaten' }},
                                {{ $province_name ?? $provinceName ?? 'Provinsi' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach



        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $village_name ?? $villageName ?? 'Desa/Kelurahan' }},
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </div>

            <!-- Tanda tangan kepala desa -->
            @if(isset($kepala_desa_signature) && !empty($kepala_desa_signature))
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $kepala_desa_signature) }}" alt="Tanda Tangan Kepala Desa" class="max-w-[200px] max-h-[100px] object-contain mx-auto">
                </div>
            @endif

            <!-- Nama kepala desa -->
            <p>{{ strtoupper($kepala_desa_name ?? $signing_name ?? 'NAMA KEPALA DESA') }}</p>
            {{-- <div class="mt-20">
                <div class="border-b border-black inline-block w-48"></div>
            </div> --}}
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
