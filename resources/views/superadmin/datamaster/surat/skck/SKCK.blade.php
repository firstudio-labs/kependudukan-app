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
            <div class="w-24 mr-4">
                <img src="/api/placeholder/100/100" alt="Logo Kota" class="w-full h-auto">
            </div>

            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($district_name ?? 'KABUPATEN') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($subdistrict_name ?? 'KECAMATAN') }}</p>
                <p class="text-2xl font-bold">
                    @if(isset($villageCode) && substr($villageCode, 0, 1) === '1')
                        KELURAHAN
                    @elseif(isset($villageCode) && substr($villageCode, 0, 1) === '2')
                        DESA
                    @else
                        {{ isset($administrationData) && isset($administrationData['village_type']) ? strtoupper($administrationData['village_type']) : 'DESA/KELURAHAN' }}
                    @endif
                    {{ strtoupper($village_name ?? 'KELURAHAN') }}
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
            <h1 class="text-lg font-bold">SURAT PENGANTAR PERMOHONAN SKCK</h1>
            <p class="text-sm">Nomor : {{ $skck->letter_number ?? '___________' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">Lurah {{ $village_name ?? 'Desa/Kelurahan' }} Kecamatan {{ $subdistrict_name ?? 'Kecamatan' }} dengan ini menerangkan bahwa :</p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $skck->full_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $skck->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $skck->birth_place ?? '-' }}</td>
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
                        <td>{{ $job_name ?? '-' }}</td>
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
                        <td>{{ $skck->address ?? '-' }} RT {{ $skck->rt ?? '0' }}, {{ $village_name ?? 'Desa/Kelurahan' }}, {{ $subdistrict_name ?? 'Kecamatan' }}, {{ $district_name ?? 'Kabupaten' }}, {{ $province_name ?? 'Provinsi' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Statement -->
        <div class="mb-6">
            <p class="mb-2">
                Berdasarkan Surat Keterangan dari Ketua RT {{ $skck->rt ?? 'XX' }}
                @if(isset($villageCode) && substr($villageCode, 0, 1) === '1')
                    Kelurahan
                @elseif(isset($villageCode) && substr($villageCode, 0, 1) === '2')
                    Desa
                @else
                    Desa/Kelurahan
                @endif
                {{ $village_name ?? 'XXXX' }}, Kecamatan {{ $subdistrict_name ?? 'XXXX' }},
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
                <p>{{ $village_name ?? 'Desa/Kelurahan' }},
                   @if(isset($formatted_letter_date) && strpos($formatted_letter_date, '-') !== false)
                       {{ \Carbon\Carbon::createFromFormat('d-m-Y', $formatted_letter_date)->locale('id')->isoFormat('D MMMM Y') }}
                   @else
                       {{ $formatted_letter_date ?? \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                   @endif
                </p>
            </div>
            <p class="font-bold">
                <p class="font-bold underline">{{ strtoupper($signing_name ?? 'NAMA KEPALA DESA') }}</p>
            </p>
        </div>
    </div>
</body>

</html>
