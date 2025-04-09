<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Rumah/Kamar Sewa - {{ $rumahSewa->full_name }}</title>
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

        <!-- Header Section -->
        <div class="flex items-center mb-4">
            <div class="w-24 h-24 mr-4 flex items-center justify-center">
                @if(isset($district_logo) && !empty($district_logo))
                    <img src="{{ asset('storage/' . $district_logo) }}" alt="Logo Kabupaten" class="max-w-[80px] max-h-[80px] object-contain">
                @else
                    <!-- Fallback to default logo -->
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Default" class="max-w-[80px] max-h-[80px] object-contain">
                @endif
            </div>

            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($districtName ?? 'KABUPATEN') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrictName ?? 'KECAMATAN') }}</p>
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
            <div class="w-24"></div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold underline">SURAT IZIN RUMAH/KAMAR SEWA</h1>
            <p class="text-sm">Nomor: {{ $rumahSewa->letter_number ?? '-' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">
                @if(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '1')
                    Lurah
                @elseif(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '2')
                    Kepala Desa
                @else
                    {{ isset($administrationData) && isset($administrationData['village_head_title']) ? $administrationData['village_head_title'] : 'Lurah/Kepala Desa' }}
                @endif
                {{ $villageName ?? 'Desa/Kelurahan' }} Kecamatan {{ $subdistrictName ?? 'Kecamatan' }} dengan ini menerangkan bahwa :
            </p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Penyelenggara</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $rumahSewa->full_name }}</td>
                    </tr>
                    <tr>
                        <td>Alamat Penyelenggara</td>
                        <td>:</td>
                        <td>
                            {{ $rumahSewa->address ?? $addressString ?? '-' }}
                            RT {{ $rumahSewa->rt ?? '0' }},
                            {{ !empty($villageName) ? $villageName : 'Desa/Kelurahan' }},
                            {{ !empty($subdistrictName) ? $subdistrictName : 'Kecamatan' }},
                            {{ !empty($districtName) ? $districtName : 'Kabupaten' }},
                            {{ !empty($provinceName) ? $provinceName : 'Provinsi' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Nama Penanggungjawab</td>
                        <td>:</td>
                        <td>{{ $rumahSewa->responsible_name }}</td>
                    </tr>
                    <tr>
                        <td>Alamat Rumah/Kamar</td>
                        <td>:</td>
                        <td>{{ $rumahSewa->rental_address ?? $rentalAddressString ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jalan</td>
                        <td>:</td>
                        <td>{{ $rumahSewa->street }}</td>
                    </tr>
                    <tr>
                        <td>Gang/Nomor</td>
                        <td>:</td>
                        <td>{{ $rumahSewa->alley_number }}</td>
                    </tr>
                    <tr>
                        <td>RT</td>
                        <td>:</td>
                        <td>{{ $rtValue ?? $rumahSewa->rt }}</td>
                    </tr>
                    <tr>
                        <td>Kelurahan</td>
                        <td>:</td>
                        <td>{{ $villageName ?? $rumahSewa->village_name }}</td>
                    </tr>
                    <tr>
                        <td>Luas Bangunan</td>
                        <td>:</td>
                        <td>{{ $rumahSewa->building_area }}</td>
                    </tr>
                    <tr>
                        <td>Jumlah Kamar</td>
                        <td>:</td>
                        <td>{{ $rumahSewa->room_count }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Rumah/Kamar Sewa</td>
                        <td>:</td>
                        <td>{{ $rumahSewa->rental_type }}</td>
                    </tr>
                    <tr>
                        <td>Berlaku Sampai</td>
                        <td>:</td>
                        <td>{{ $validUntilDate }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ $villageName ?? 'Desa/Kelurahan' }},
                @if(isset($formatted_letter_date) && strpos($formatted_letter_date, '-') !== false)
                    {{ \Carbon\Carbon::createFromFormat('d-m-Y', $formatted_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    {{ $formatted_letter_date ?? \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
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
