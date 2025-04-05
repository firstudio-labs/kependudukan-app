@props(['data'])

<div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
        @php
            $currentPage = $data->currentPage();
            $itemsPerPage = $data->perPage();
            $totalItems = $data->total();
            $startNumber = ($currentPage - 1) * $itemsPerPage + 1;
            $endNumber = min($startNumber + $itemsPerPage - 1, $totalItems);
        @endphp
        Showing {{ $startNumber }} to {{ $endNumber }} of {{ $totalItems }} results
    </div>
    @if($data->lastPage() > 1)
        <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
            @php
                $totalPages = $data->lastPage();
                $currentPage = $data->currentPage();

                // Logic for showing page numbers
                $startPage = 1;
                $endPage = $totalPages;
                $maxVisible = 7; // Number of visible page links excluding Previous/Next

                if ($totalPages > $maxVisible) {
                    $halfVisible = floor($maxVisible / 2);
                    $startPage = max($currentPage - $halfVisible, 1);
                    $endPage = min($startPage + $maxVisible - 1, $totalPages);

                    if ($endPage - $startPage < $maxVisible - 1) {
                        $startPage = max($endPage - $maxVisible + 1, 1);
                    }
                }
            @endphp

            <!-- Previous Button -->
            @if($currentPage > 1)
                <a href="?page={{ $currentPage - 1 }}&{{ http_build_query(request()->except('page')) }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    Previous
                </a>
            @endif

            <!-- First Page -->
            @if($startPage > 1)
                <a href="?page=1&{{ http_build_query(request()->except('page')) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    1
                </a>
                @if($startPage > 2)
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                @endif
            @endif

            <!-- Page Numbers -->
            @for($i = $startPage; $i <= $endPage; $i++)
                <a href="?page={{ $i }}&{{ http_build_query(request()->except('page')) }}"
                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium
                   {{ $i == $currentPage ? 'z-10 bg-blue-50 border-blue-500 text-[#8c93d6]' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    {{ $i }}
                </a>
            @endfor

            <!-- Last Page -->
            @if($endPage < $totalPages)
                @if($endPage < $totalPages - 1)
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                @endif
                <a href="?page={{ $totalPages }}&{{ http_build_query(request()->except('page')) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ $totalPages }}
                </a>
            @endif

            <!-- Next Button -->
            @if($currentPage < $totalPages)
                <a href="?page={{ $currentPage + 1 }}&{{ http_build_query(request()->except('page')) }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    Next
                </a>
            @endif
        </nav>
    @endif
</div>
