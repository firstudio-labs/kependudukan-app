<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan SKCK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap');

        body {
            font-family: 'Times New Roman', Times, serif;
        }

        @media print {
            /* Hide browser UI elements */
            .no-print {
                display: none !important;
            }

            /* General print styling */
            body {
                margin: 0;
                padding: 15px;
            }

            /* Page break settings */
            .pagebreak {
                page-break-before: always;
            }

            /* Hide URL from appearing in footer */
            @page {
                margin: 0.5cm;
                size: auto;
            }

            /* Hide URL/footer content but keep page numbers */
            @page :footer {
                content: counter(page) " / " counter(pages);
                font-family: 'Times New Roman', Times, serif;
                font-size: 10pt;
            }
        }
    </style>
</head>

<body class="bg-white p-8">
    <div class="max-w-4xl mx-auto bg-white p-8">
        <div class="no-print flex justify-end mb-4">
            <button onclick="window.print()" class="bg-[#2D336B] text-white px-4 py-2 rounded hover:bg-[#5C69A7]">
                Cetak Dokumen
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
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($district_name ?? 'KABUPATEN') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrict_name ?? 'KECAMATAN') }}</p>
                <p class="text-2xl font-bold">
                    @if(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '1')
                        KELURAHAN
                    @elseif(isset($villageCode) && strlen($villageCode) >= 7 && substr($villageCode, 6, 1) === '2')
                        DESA
                    @else
                        {{ isset($administrationData) && isset($administrationData['village_type']) ? strtoupper($administrationData['village_type']) : 'DESA/KELURAHAN' }}
                    @endif
                    {{ strtoupper($village_name ?? 'KELURAHAN') }}
                </p>
                <p class="text-sm">Alamat: {{ ucwords(strtolower($village_name ?? 'XXXX')) }}, {{ ucwords(strtolower($subdistrict_name ?? 'XXXX')) }}, {{ ucwords(strtolower($district_name ?? 'XXXX')) }}</p>
            </div>
            <div class="w-24">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold">SURAT PENGANTAR PERMOHONAN SKCK</h1>
            <p class="text-sm">Nomor : {{ $skck->letter_number ?? '___________' }}</p>
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
                {{ ucwords(strtolower($village_name ?? 'Desa/Kelurahan')) }} Kecamatan {{ ucwords(strtolower($subdistrict_name ?? 'Kecamatan')) }} dengan ini menerangkan bahwa :
            </p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td>{{ ucwords(strtolower($skck->full_name ?? '-')) }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $skck->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ ucwords(strtolower($skck->birth_place ?? '-')) }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            @if(isset($formatted_birth_date) && strpos($formatted_birth_date, '-') !== false)
                                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $formatted_birth_date)->locale('id')->isoFormat('D MMMM Y') }}
                            @else
                                {{ $formatted_birth_date ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $gender ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ ucwords(strtolower($job_name ?? '-')) }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $religion ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kewarganegaraan</td>
                        <td>:</td>
                        <td>{{ $citizenship ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ ucwords(strtolower($skck->address ?? '-')) }} RT {{ $skck->rt ?? '0' }}, {{ ucwords(strtolower($village_name ?? 'Desa/Kelurahan')) }}, {{ ucwords(strtolower($subdistrict_name ?? 'Kecamatan')) }}, {{ ucwords(strtolower($district_name ?? 'Kabupaten')) }}, {{ ucwords(strtolower($province_name ?? 'Provinsi')) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Statement -->
        <div class="mb-6">
            <p class="mb-2">
                Berdasarkan Surat Keterangan dari Ketua RT {{ $skck->rt ?? 'XX' }}
                {{ ucwords(strtolower($skck->address ?? '-')) }},
                {{ ucwords(strtolower($village_name ?? 'XXXX')) }},
                {{ ucwords(strtolower($subdistrict_name ?? 'XXXX')) }},
                {{ ucwords(strtolower($district_name ?? 'XXXX')) }},
                {{ ucwords(strtolower($province_name ?? 'XXXX')) }},
                Tanggal
                @if(isset($formatted_letter_date) && !empty($formatted_letter_date))
                    {{ \Carbon\Carbon::parse($formatted_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    XX-XX-XXXX
                @endif
                bahwa yang bersangkutan sepanjang pengetahuan berkelakuan baik.
            </p>
            <p>Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan {{ $skck->purpose ?? 'sebagaimana mestinya' }}.</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                {{ ucwords(strtolower($village_name ?? 'Desa/Kelurahan')) }},
                @if(isset($formatted_letter_date) && !empty($formatted_letter_date))
                    {{ \Carbon\Carbon::parse($formatted_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
                @else
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                @endif
            </div>

            <!-- Signing Name -->
            @if(isset($signing_name) && !empty($signing_name))
                <div class="mb-4">
                    <p>{{ strtoupper($signing_name) }}</p>
                </div>
            @endif

            <!-- Tanda tangan kepala desa -->
            @if(isset($kepala_desa_signature) && !empty($kepala_desa_signature))
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $kepala_desa_signature) }}" alt="Tanda Tangan Kepala Desa" class="max-w-[200px] max-h-[100px] object-contain mx-auto">
                </div>
            @endif

            <!-- Nama kepala desa -->
            <p>{{ strtoupper($kepala_desa_name ?? 'NAMA KEPALA DESA') }}</p>
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
