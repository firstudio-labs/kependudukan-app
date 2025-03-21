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
    </style>
</head>

<body class="bg-white p-8">
    <div class="max-w-4xl mx-auto bg-white p-8">
        <div class="flex items-center mb-4">
            <div class="w-24 mr-4">
                <img src="/api/placeholder/100/100" alt="Logo Kota" class="w-full h-auto">
            </div>
            <div class="flex-1 text-center">
                <p class="text-lg font-bold">PEMERINTAH KABUPATEN</p>
                <p class="text-lg font-bold">KECAMATAN</p>
                <p class="text-2xl font-bold">KELURAHAN</p>
                <p class="text-sm">Alamat:</p>

            </div>
            <div class="w-24">
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-black mb-6"></div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-lg font-bold">SURAT KETERANGAN AHLI WARIS</h1>
            <p class="text-sm">Nomor :</p>
        </div>

        <!-- Introduction -->
        <div class="mb-6">
            <p class="mb-4">Kepala Desa/Lurah Kecamatan dengan ini menerangkan bahwa :</p>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="w-1/3">Nama Lengkap</td>
                        <td class="w-1/12">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature -->
        <div class="text-center mt-16">
            <div class="mb-4">
                <span class="bg-yellow-200 px-2 py-1">Tempat, <span id="currentDate"><?php echo date('d F Y'); ?></span></span>
            </div>
            <p class="font-bold">KEPALA DESA</p>
            <div class="mt-20">
                <!-- Space for signature -->
                <p class="font-bold underline">(NAMA KEPALA DESA)</p>
                <span class="bg-yellow-200 px-2 py-1">NIP. __________________</span>
            </div>
        </div>

        <script>
            // Set the date in Indonesian format
            document.addEventListener('DOMContentLoaded', function() {
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const now = new Date();
                const formattedDate = now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
                document.getElementById('currentDate').textContent = formattedDate;

                // Also convert any dates that might be in DD-MM-YYYY format
                const formatDate = (dateStr) => {
                    if (!dateStr || dateStr.includes(' ')) return dateStr; // Already formatted or empty

                    const parts = dateStr.split('-');
                    if (parts.length === 3) {
                        // Convert from DD-MM-YYYY to DD Month YYYY
                        return parts[0] + ' ' + months[parseInt(parts[1])-1] + ' ' + parts[2];
                    }
                    return dateStr;
                };

                // Find all elements with date content and format them
                document.querySelectorAll('.format-date').forEach(el => {
                    el.textContent = formatDate(el.textContent);
                });
            });
        </script>
    </div>
</body>

</html>
