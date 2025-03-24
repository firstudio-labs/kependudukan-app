<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Permohonan KTP - {{ $fullName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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

<body class="bg-gray-100 p-4">
    <!-- Print Button - Only visible on screen -->
    <div class="no-print mb-4 flex justify-end max-w-4xl mx-auto">
        <button onclick="window.print()" class="bg-[#2D336B] text-white px-4 py-2 rounded hover:bg-[#5C69A7]">
            <i class="fa-solid fa-print mr-2"></i> Cetak Dokumen
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white mb-16 p-6">
        <h1 class="text-center font-bold text-lg mb-4">FORMULIR PERMOHONAN KARTU TANDA PENDUDUK (WNI) WARGA</h1>

        <!-- Perhatian Box -->
        <div class="border border-black p-2 mb-4">
            <p class="font-bold">PERHATIAN:</p>
            <ol class="list-decimal pl-6">
                <li>Harap di isi dengan huruf cetak dan menggunakan tinta hitam</li>
                <li>Untuk kolom pilihan, harap memberi tanda silang (X) pada kotak pilihan</li>
                <li>Setelah formulir ini di isi dan ditandatangani, harap diserahkan kembali ke kantor desa/kelurahan
                </li>
            </ol>
        </div>

        <!-- Administrative Information -->
        <div class="mb-4">
            <div class="flex mb-1">
                <div class="w-1/4">PEMERINTAH PROVINSI</div>
                <div class="w-1/12 text-center">:</div>
                <div class="w-3/5 border border-black px-2 py-1">{{ strtoupper($provinceName) }}</div>
            </div>
            <div class="flex mb-1">
                <div class="w-1/4">PEMERINTAH KABUPATEN/KOTA</div>
                <div class="w-1/12 text-center">:</div>
                <div class="w-3/5 border border-black px-2 py-1">{{ strtoupper($districtName) }}</div>
            </div>
            <div class="flex mb-1">
                <div class="w-1/4">KECAMATAN</div>
                <div class="w-1/12 text-center">:</div>
                <div class="w-3/5 border border-black px-2 py-1">{{ strtoupper($subdistrictName) }}</div>
            </div>
            <div class="flex mb-1">
                <div class="w-1/4">KELURAHAN</div>
                <div class="w-1/12 text-center">:</div>
                <div class="w-3/5 border border-black px-2 py-1">{{ strtoupper($villageName) }}</div>
            </div>
            <div class="flex mb-1">
                <div class="w-1/4">PERMOHONAN KTP</div>
                <div class="w-1/12 text-center">:</div>
                <div class="w-3/5 flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="border border-black w-5 h-5 mr-2 flex items-center justify-center">
                            @if($applicationType == 'Baru')
                                <span class="text-lg">X</span>
                            @endif
                        </div>
                        <span>1. Baru</span>
                    </div>
                    <div class="flex items-center">
                        <div class="border border-black w-5 h-5 mr-2 flex items-center justify-center">
                            @if($applicationType == 'Perpanjang')
                                <span class="text-lg">X</span>
                            @endif
                        </div>
                        <span>2. Perpanjangan</span>
                    </div>
                    <div class="flex items-center">
                        <div class="border border-black w-5 h-5 mr-2 flex items-center justify-center">
                            @if($applicationType == 'Pergantian')
                                <span class="text-lg">X</span>
                            @endif
                        </div>
                        <span>3. Penggantian</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <div class="flex mb-2">
                <div class="w-1/5 border border-black p-1 mr-4">Nama Lengkap</div>
                <div class="w-4/5 ml-2">
                    <div class="w-full">
                        <div class="flex space-x-1">
                            @php
                                // Convert name to uppercase and pad to 30 characters
                                $nameChars = str_pad(strtoupper($fullName ?? ''), 30, ' ');
                                // Split into array of characters
                                $nameChars = mb_str_split($nameChars);
                            @endphp

                            @foreach(array_slice($nameChars, 0, 23) as $char)
                                <div class="border border-black w-6 h-6 flex items-center justify-center">{{ $char }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex mb-2">
                <div class="w-1/5 border border-black p-1 mr-4">NIK</div>
                <div class="w-4/5 ml-2">
                    <div class="w-full">
                        <div class="flex space-x-1">
                            @php
                                // Pad NIK to ensure 16 characters
                                $nikChars = str_pad($nik ?? '', 16, ' ');
                                // Split into array of characters
                                $nikChars = mb_str_split($nikChars);
                            @endphp

                            @foreach($nikChars as $char)
                                <div class="border border-black w-6 h-6 flex items-center justify-center">{{ $char }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex mb-2">
                <div class="w-1/5 border border-black p-1 mr-4">No. KK</div>
                <div class="w-4/5 ml-2">
                    <div class="w-full">
                        <div class="flex space-x-1">
                            @php
                                // Pad KK to ensure 16 characters
                                $kkChars = str_pad($ktp->kk ?? '', 16, ' ');
                                // Split into array of characters
                                $kkChars = mb_str_split($kkChars);
                            @endphp

                            @foreach($kkChars as $char)
                                <div class="border border-black w-6 h-6 flex items-center justify-center">{{ $char }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex mb-2">
                <div class="w-1/5 border border-black p-1 mr-4">Alamat</div>
                <div class="w-4/5 ml-2">
                    <div class="border border-black h-8 p-1">{{ $ktp->address ?? '' }}</div>
                </div>
            </div>

            <div class="flex mb-4">
                <div class="w-1/5"></div>
                <div class="w-4/5 ml-2 flex gap-6">
                    <div class="flex items-center">
                        <span class="mr-2">RT</span>
                        <div class="flex space-x-1">
                            @php
                                // Pad RT to ensure 3 characters
                                $rtChars = str_pad($ktp->rt ?? '', 3, ' ');
                                // Split into array of characters
                                $rtChars = mb_str_split($rtChars);
                            @endphp

                            @foreach($rtChars as $char)
                                <div class="border border-black w-6 h-6 flex items-center justify-center">{{ $char }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">RW</span>
                        <div class="flex space-x-1">
                            @php
                                // Pad RW to ensure 3 characters
                                $rwChars = str_pad($ktp->rw ?? '', 3, ' ');
                                // Split into array of characters
                                $rwChars = mb_str_split($rwChars);
                            @endphp

                            @foreach($rwChars as $char)
                                <div class="border border-black w-6 h-6 flex items-center justify-center">{{ $char }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">Kode Pos</span>
                        <div class="flex space-x-1">
                            <div class="border border-black w-6 h-6 flex items-center justify-center">-</div>
                            <div class="border border-black w-6 h-6 flex items-center justify-center">-</div>
                            <div class="border border-black w-6 h-6 flex items-center justify-center">-</div>
                            <div class="border border-black w-6 h-6 flex items-center justify-center">-</div>
                            <div class="border border-black w-6 h-6 flex items-center justify-center">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <table class="border-collapse border border-black w-3/5">
                <tr>
                    <td class="border border-black p-2 text-center align-top h-28 w-1/4">
                        <div class="mb-1">Pas Photo 2x3</div>
                        <div class="h-20 "></div>
                    </td>
                    <td class="border border-black p-2 text-center align-top h-28 w-1/4">
                        <div class="mb-1">Cap Jempol</div>
                        <div class="h-20 "></div>
                    </td>
                    <td class="border border-black p-2 text-center align-top h-28 w-3/5">
                        <div class="mb-1">Tanda Tangan</div>
                        <div class="h-20"></div>
                    </td>
                </tr>
            </table>

            <div class="w-1/3 text-center">
                <p>{{ $villageName }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p>Pemohon</p>
                <div class="h-16"></div>
                <p class="underline">{{ $fullName }}</p>
                <p class="mt-6">Mengetahui,</p>
                <p>Lurah {{ $villageName }}</p>
                <div class="h-16"></div>
                <p class="underline">{{ $ktp->signing ?? 'KEPALA DESA' }}</p>
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
