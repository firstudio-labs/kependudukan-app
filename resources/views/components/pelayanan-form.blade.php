<!-- Wrapper Flex -->
<div class="flex flex-col md:flex-row gap-6">
    <!-- Card Nomor Antrian -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-md border border-white/20 p-6 text-center w-full md:w-1/3 self-start">
        <button class="text-black font-semibold px-4 py-2 rounded-xl mb-4 bg-white/10 backdrop-blur-lg border border-white/20 shadow-sm">
            Antrian Layanan Desa
        </button>

        <div class="border border-white/20 rounded-2xl p-6 bg-white/5 backdrop-blur-lg shadow-inner">
            <div class="text-sm text-black mb-1"></div>
            @if(session('no_antrian'))
                <div class="text-5xl font-bold text-black drop-shadow-md">{{ session('no_antrian') }}</div>
                <div class="mt-2 text-[#a7a7ee] text-sm">Nomor antrian anda</div>
                @if(session('village_name'))
                    <div class="mt-1 text-sm text-gray-200">Desa: {{ session('village_name') }}</div>
                @endif
            @else
                <div class="text-5xl font-bold text-black drop-shadow-md">-</div>
            @endif
        </div>

        <p class="mt-4 text-sm italic text-black">Quod Enchiridion Epictetus stoici scripsit. Rodrigo Abela</p>
    </div>

    <!-- Form Langsung -->
    <div class="w-full md:w-3/5 md:ml-10">
        @component('components.pelayanan-form', [
            'provinces' => $provinces,
            'keperluanList' => $keperluanList,
            'selected_province_id' => old('province_id'),
            'selected_district_id' => old('district_id'),
            'selected_sub_district_id' => old('sub_district_id'),
            'selected_village_id' => old('village_id')
        ])
        @endcomponent
    </div>
</div>

<!-- Include necessary scripts for component -->
<script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
<script src="{{ asset('js/location-selector.js') }}"></script>

<script>
    // Show success message if available
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showSuccessAlert("{{ session('success') }}");
        @endif
    });

    $(document).ready(function() {
        // Update the submission handler to redirect to appropriate service URL
        $('#keperluan').change(function() {
            const selectedValue = $(this).val();
            const selectedText = $(this).find("option:selected").text().toLowerCase();
            const serviceType = extractServiceType(selectedText);

            if (serviceType) {
                // Store the service URL with query parameters in a data attribute
                const provinceId = $('#province_id').val();
                const districtId = $('#district_id').val();
                const subDistrictId = $('#sub_district_id').val();
                const villageId = $('#village_id').val();

                const serviceUrl = `/pelayanan/${serviceType}?province_id=${provinceId}&district_id=${districtId}&sub_district_id=${subDistrictId}&village_id=${villageId}`;

                // Store the URL for later use during form submission
                $(this).data('service-url', serviceUrl);
            }
        });

        // Helper function to extract service type from keperluan text
        function extractServiceType(text) {
            if (text.includes('administrasi')) return 'administrasi';
            if (text.includes('kehilangan')) return 'kehilangan';
            if (text.includes('skck')) return 'skck';
            // Add more mappings as needed
            return null;
        }

        // Intercept form submission to use the query parameter URL when appropriate
        $('form').submit(function(e) {
            const serviceUrl = $('#keperluan').data('service-url');
            if (serviceUrl) {
                e.preventDefault();
                window.location.href = serviceUrl;
                return false;
            }
        });
    });
</script>
