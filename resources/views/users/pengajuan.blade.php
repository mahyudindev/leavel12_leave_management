<x-app-layout>
    @if(auth()->user()->role === 'manager')
    <x-admin-sidebar />
    @endif

    <div class="@if(auth()->user()->role === 'manager') p-4 sm:ml-64 @else max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5 @endif">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Pengajuan Cuti</h3>
                @if(session('success'))
                    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 dark:bg-green-900 dark:border-green-700 dark:text-green-300" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Close</title>
                                <path d="M14.348 14.849a1 1 0 01-1.414 0L10 11.414l-2.934 2.935a1 1 0 01-1.414-1.415l2.935-2.934-2.935-2.934a1 1 0 011.415-1.415L10 8.586l2.934-2.935a1 1 0 011.415 1.415L11.414 10l2.935 2.934a1 1 0 010 1.415z"/>
                            </svg>
                        </span>
                    </div>
                    <script>
                        setTimeout(() => {
                            document.getElementById('success-alert').style.display = 'none';
                        }, 3000);
                    </script>
                @endif
                @if(session('error'))
                    <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-300" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Close</title>
                                <path d="M14.348 14.849a1 1 0 01-1.414 0L10 11.414l-2.934 2.935a1 1 0 01-1.414-1.415l2.935-2.934-2.935-2.934a1 1 0 011.415-1.415L10 8.586l2.934-2.935a1 1 0 011.415 1.415L11.414 10l2.935 2.934a1 1 0 010 1.415z"/>
                            </svg>
                        </span>
                    </div>
                    <script>
                        setTimeout(() => {
                            document.getElementById('error-alert').style.display = 'none';
                        }, 3000);
                    </script>
                @endif
                <form action="{{ route('cuti.ajukan') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="jenis_cuti" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Cuti</label>
                        <select id="jenis_cuti" name="jenis_cuti" class="block w-full mt-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @foreach($jenisCuti as $jenis)
                                <option value="{{ $jenis->jenis_cuti_id }}">{{ $jenis->nama_cuti }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_awal_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Awal</label>
                        <input type="text" id="tanggal_awal_display" class="block w-full mt-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="DD/MM/YYYY" autocomplete="off">
                        <input type="hidden" id="tanggal_awal" name="tanggal_awal">
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_akhir_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                        <input type="text" id="tanggal_akhir_display" class="block w-full mt-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="DD/MM/YYYY" autocomplete="off">
                        <input type="hidden" id="tanggal_akhir" name="tanggal_akhir">
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
                        Ajukan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Indonesian locale
            flatpickr.localize(flatpickr.l10ns.id);
            
            // Initialize date pickers
            const tanggalAwalPicker = flatpickr("#tanggal_awal_display", {
                dateFormat: "d/m/Y",
                allowInput: true,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        // Format for backend: YYYY-MM-DD
                        const formattedDate = selectedDates[0].toISOString().split('T')[0];
                        document.getElementById('tanggal_awal').value = formattedDate;
                    }
                }
            });
            
            const tanggalAkhirPicker = flatpickr("#tanggal_akhir_display", {
                dateFormat: "d/m/Y",
                allowInput: true,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        // Format for backend: YYYY-MM-DD
                        const formattedDate = selectedDates[0].toISOString().split('T')[0];
                        document.getElementById('tanggal_akhir').value = formattedDate;
                    }
                }
            });
            
            // Apply dark mode to flatpickr if page is in dark mode
            const htmlElement = document.documentElement;
            if (htmlElement.classList.contains('dark')) {
                document.querySelectorAll('.flatpickr-calendar').forEach(calendar => {
                    calendar.classList.add('dark');
                });
            }
            
            // Form submission validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const tanggalAwal = document.getElementById('tanggal_awal').value;
                const tanggalAkhir = document.getElementById('tanggal_akhir').value;
                
                if (!tanggalAwal || !tanggalAkhir) {
                    e.preventDefault();
                    alert('Silakan pilih tanggal awal dan akhir cuti.');
                }
            });
        });
    </script>
</x-app-layout>
