<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kelahiran</title>
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
        <!-- Print Button - Only visible on screen (moved to top like in Kehilangan.blade.php) -->
        <div class="no-print mb-4 flex justify-end">
            <button onclick="window.print()" class="bg-[#2D336B] text-white px-4 py-2 rounded hover:bg-[#5C69A7]">
                <i class="fa-solid fa-print mr-2"></i> Cetak Dokumen
            </button>
        </div>

        <div class="flex items-center mb-2">
            <div class="w-20 mr-2">
                <img src="/api/placeholder/100/100" alt="Logo Kota" class="w-full h-auto">
            </div>
            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH {{ strtoupper($kelahiran->district_name ?? '') }}</p>
                <p class="text-lg font-bold">KECAMATAN {{ strtoupper($kelahiran->subdistrict_name ?? '') }}</p>
                <p class="text-xl font-bold">
                    @if(isset($kelahiran->village_code) && substr($kelahiran->village_code, 0, 1) === '1')
                        KELURAHAN
                    @elseif(isset($kelahiran->village_code) && substr($kelahiran->village_code, 0, 1) === '2')
                        DESA
                    @else
                        {{ isset($kelahiran) && isset($kelahiran->village_type) ? strtoupper($kelahiran->village_type) : 'DESA/KELURAHAN' }}
                    @endif
                    {{ strtoupper($kelahiran->village_name ?? '') }}
                </p>
                <p class="text-xs">Alamat: </p>
            </div>
            <div class="w-20">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-3"></div>

        <!-- Document Title -->
        <div class="text-center mb-3">
            <h1 class="text-lg font-bold underline">SURAT KETERANGAN KELAHIRAN</h1>
            <p class="text-sm">Nomor: {{ $kelahiran->letter_number ?? '-' }}</p>
        </div>

        <!-- Introduction -->
        <div class="mb-2">
            <p class="mb-2 font-bold">Diberikan kepada:</p>
        </div>

        <!-- Father Information -->
        <div class="mb-2">
            <table class="w-full text-sm">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Ayah</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $kelahiran->father_full_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $kelahiran->father_nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $kelahiran->father_birth_place ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            @if(isset($kelahiran->formatted_father_birth_date) && strpos($kelahiran->formatted_father_birth_date, '-') !== false)
                                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $kelahiran->formatted_father_birth_date)->locale('id')->isoFormat('D MMMM Y') }}
                            @else
                                {{ $kelahiran->formatted_father_birth_date ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $kelahiran->father_job_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $kelahiran->father_religion_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>
                            {{ $kelahiran->father_address ?? '-' }}
                            RT {{ $kelahiran->father_rt ?? '0' }},
                            {{ !empty($kelahiran->village_name) ? $kelahiran->village_name : 'Desa/Kelurahan' }},
                            {{ !empty($kelahiran->subdistrict_name) ? $kelahiran->subdistrict_name : 'Kecamatan' }},
                            {{ !empty($kelahiran->district_name) ? $kelahiran->district_name : 'Kabupaten' }},
                            {{ !empty($kelahiran->province_name) ? $kelahiran->province_name : 'Provinsi' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mother Information -->
        <div class="mb-2">
            <table class="w-full text-sm">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Ibu</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $kelahiran->mother_full_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $kelahiran->mother_nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $kelahiran->mother_birth_place ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            @if(isset($kelahiran->formatted_mother_birth_date) && strpos($kelahiran->formatted_mother_birth_date, '-') !== false)
                                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $kelahiran->formatted_mother_birth_date)->locale('id')->isoFormat('D MMMM Y') }}
                            @else
                                {{ $kelahiran->formatted_mother_birth_date ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $kelahiran->mother_job_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $kelahiran->mother_religion_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>
                            {{ $kelahiran->mother_address ?? '-' }}
                            RT {{ $kelahiran->mother_rt ?? '0' }},
                            {{ !empty($kelahiran->village_name) ? $kelahiran->village_name : 'Desa/Kelurahan' }},
                            {{ !empty($kelahiran->subdistrict_name) ? $kelahiran->subdistrict_name : 'Kecamatan' }},
                            {{ !empty($kelahiran->district_name) ? $kelahiran->district_name : 'Kabupaten' }},
                            {{ !empty($kelahiran->province_name) ? $kelahiran->province_name : 'Provinsi' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Introduction -->
        <div class="mb-2">
            <p class="mb-2 font-bold">Telah lahir seorang anak:</p>
        </div>

        <!-- Child Information -->
        <div class="mb-2">
            <table class="w-full text-sm">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Anak</td>
                        <td class="w-1/12">:</td>
                        <td>{{ $kelahiran->child_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td>{{ $kelahiran->child_birth_place ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            @if(isset($kelahiran->formatted_child_birth_date) && strpos($kelahiran->formatted_child_birth_date, '-') !== false)
                                {{ \Carbon\Carbon::createFromFormat('d-m-Y', $kelahiran->formatted_child_birth_date)->locale('id')->isoFormat('D MMMM Y') }}
                            @else
                                {{ $kelahiran->formatted_child_birth_date ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $kelahiran->child_gender_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $kelahiran->child_religion_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>
                            {{ $kelahiran->child_address ?? '-' }}
                            RT {{ $kelahiran->child_rt ?? '0' }},
                            {{ !empty($kelahiran->village_name) ? $kelahiran->village_name : 'Desa/Kelurahan' }},
                            {{ !empty($kelahiran->subdistrict_name) ? $kelahiran->subdistrict_name : 'Kecamatan' }},
                            {{ !empty($kelahiran->district_name) ? $kelahiran->district_name : 'Kabupaten' }},
                            {{ !empty($kelahiran->province_name) ? $kelahiran->province_name : 'Provinsi' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Anak Ke</td>
                        <td>:</td>
                        <td>{{ $kelahiran->child_order ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-8">
            <p>Demikian surat Keterangan ini dibuat dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-2">
            <div class="mb-3">
                <p>{{ $kelahiran->village_name ?? '' }}, {{ $currentDate ?? \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            </div>
            <p class="font-bold">
                <p class="font-bold underline">{{ strtoupper($signing_name ?? 'NAMA KEPALA DESA') }}</p>
            </p>

        </div>

        <!-- Removed the bottom print button since we've added it to the top -->
    </div>

    <script>
        // Auto-print when the page loads (optional)
        window.onload = function() {
            // Uncomment this line if you want the print dialog to appear automatically
            // window.print();

            // Format any client-side dates in Indonesian
            const formatDateID = (dateStr) => {
                if (!dateStr) return '';
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const d = new Date(dateStr);
                return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
            };

            // Apply to any dynamically inserted dates
            document.querySelectorAll('[data-format-date]').forEach(el => {
                if (el.textContent) {
                    try {
                        el.textContent = formatDateID(el.textContent);
                    } catch (e) {
                        console.error('Error formatting date', e);
                    }
                }
            });
        };
    </script>
</body>

</html>
