<x-app-layout>
    <x-admin-sidebar />

    <div class="p-4 sm:ml-64">
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: "{{ session('success') }}",
                        icon: "success",
                        timer: 2500,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: "{{ session('error') }}",
                        icon: "error",
                        timer: 2500,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif

        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-xs">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Daftar Pengajuan Cuti - Status: {{ ucfirst($status) }}</h2>
                <div class="flex space-x-2">
                    @if(auth()->user()->role === 'manager')
                        <a href="{{ route('admin.cuti.status', 'pending') }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            Menunggu Persetujuan
                        </a>
                        
                        <a href="{{ route('admin.cuti.status', 'approved') }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'approved' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            Disetujui
                        </a>
                        <a href="{{ route('admin.cuti.status', 'rejected') }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            Ditolak
                        </a>
                    @elseif(auth()->user()->role === 'hrd')
                        <a href="{{ route('admin.cuti.status', 'pending') }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            Perlu Review
                        </a>
                        <a href="{{ route('admin.cuti.status', 'approved') }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'approved' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            Disetujui
                        </a>
                        <a href="{{ route('admin.cuti.status', 'rejected') }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            Ditolak
                        </a>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden mb-8 w-full rounded-lg border dark:border-gray-700 shadow-xs">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                                <th class="px-4 py-3">NIK</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Departemen</th>
                                <th class="px-4 py-3">Jenis Cuti</th>
                                <th class="px-4 py-3">Tanggal Mulai</th>
                                <th class="px-4 py-3">Tanggal Selesai</th>
                                <th class="px-4 py-3">Durasi</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y dark:divide-gray-700">
                            @forelse($daftarCuti as $cuti)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="px-4 py-3 text-sm">{{ $cuti->user->nik }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $cuti->user->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $cuti->user->departemen->nama }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $cuti->jenisCuti->nama_cuti }}</td>
                                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($cuti->tanggal_awal)->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($cuti->tanggal_akhir)->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $cuti->jumlah }} Hari</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 text-sm rounded-full
                                            @if($cuti->status === 'Pending')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @elseif($cuti->status === 'Approved')
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($cuti->status === 'Rejected')
                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                            @endif">
                                            @if($cuti->status === 'Pending')
                                                Menunggu Persetujuan
                                            @elseif($cuti->status === 'Approved')
                                                Disetujui
                                            @elseif($cuti->status === 'Rejected')
                                                Ditolak
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex space-x-2 items-center">
                                            @if(auth()->user()->role === 'manager' && $cuti->status_manager === 'Pending')
                                                <form action="{{ route('admin.cuti.update-status', $cuti->cuti_id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                                        Setuju
                                                    </button>
                                                </form>
                                                <button onclick="showRejectModal('{{ $cuti->cuti_id }}')" class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                                    Tolak
                                                </button>
                                            @elseif(auth()->user()->role === 'hrd' && $cuti->status_hrd === 'Pending')    
                                                <form action="{{ route('admin.cuti.update-status', $cuti->cuti_id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                                        Setuju
                                                    </button>
                                                </form>
                                                <button onclick="showRejectModal('{{ $cuti->cuti_id }}')" class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                                    Tolak
                                                </button>
                                            @endif
                                            <button onclick="showDetailModal('{{ $cuti->cuti_id }}')" class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                                Detail
                                            </button>
                                            @if($cuti->notes_manager)
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Catatan Manager: {{ $cuti->notes_manager }}</span>
                                            @endif
                                            @if($cuti->notes_hrd)
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Catatan HRD: {{ $cuti->notes_hrd }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                                        @if(auth()->user()->role === 'manager')
                                            @if($status === 'Pending')
                                                Tidak ada pengajuan cuti yang perlu disetujui dari departemen Anda.
                                            @else
                                                Tidak ada data cuti {{ $status }} dari departemen Anda.
                                            @endif
                                        @elseif(auth()->user()->role === 'hrd')
                                            @if($status === 'Approved')
                                                Tidak ada pengajuan cuti yang sudah disetujui manager.
                                            @else
                                                Tidak ada data cuti {{ $status }}.
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $daftarCuti->links('pagination::tailwind') }}
            </div>
        </div>

        <!-- Reject Modals -->
        @foreach($daftarCuti as $cuti)
            <div id="reject-modal-{{ $cuti->cuti_id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Alasan Penolakan</h3>
                        <form action="{{ route('admin.cuti.update-status', $cuti->cuti_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="reject">
                            <textarea name="notes" class="w-full px-3 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:focus:border-blue-500" rows="4" required
                                placeholder="Masukkan alasan penolakan..."></textarea>
                            <div class="flex justify-end mt-4 space-x-2">
                                <button type="button" onclick="document.getElementById('reject-modal-{{ $cuti->cuti_id }}').classList.add('hidden')"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                    Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Detail Modals -->
        @foreach($daftarCuti as $cuti)
            <div id="detail-modal-{{ $cuti->cuti_id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                <div class="relative top-20 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Pengajuan Cuti</h3>
                            <button onclick="document.getElementById('detail-modal-{{ $cuti->cuti_id }}').classList.add('hidden')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">NIK</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $cuti->user->nik }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Nama</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $cuti->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Departemen</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $cuti->user->departemen->nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Cuti</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $cuti->jenisCuti->nama_cuti }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ \Carbon\Carbon::parse($cuti->tanggal_awal)->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Selesai</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ \Carbon\Carbon::parse($cuti->tanggal_akhir)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Status Manager</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 text-sm rounded-full
                                                @if($cuti->status_manager === 'Pending')
                                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($cuti->status_manager === 'Approved')
                                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($cuti->status_manager === 'Rejected')
                                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @endif">
                                                {{ ucfirst($cuti->status_manager) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Status HRD</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 text-sm rounded-full
                                                @if($cuti->status_hrd === 'Pending')
                                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($cuti->status_hrd === 'Approved')
                                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($cuti->status_hrd === 'Rejected')
                                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @endif">
                                                {{ ucfirst($cuti->status_hrd) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Approval Manager</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $cuti->approved_at_manager ? \Carbon\Carbon::parse($cuti->approved_at_manager)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Approval HRD</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $cuti->approved_at_hrd ? \Carbon\Carbon::parse($cuti->approved_at_hrd)->format('d-m-Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Catatan Manager</td>
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-900 dark:text-gray-300">{{ $cuti->notes_manager ?: '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Catatan HRD</td>
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-900 dark:text-gray-300">{{ $cuti->notes_hrd ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengajuan</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ \Carbon\Carbon::parse($cuti->created_at)->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diupdate</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ \Carbon\Carbon::parse($cuti->updated_at)->format('d-m-Y') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showRejectModal(id) {
            event.stopPropagation();
            const modal = document.querySelector(`#reject-modal-${id}`);
            modal.classList.remove('hidden');
        }

        function showDetailModal(id) {
            const modal = document.querySelector(`#detail-modal-${id}`);
            modal.classList.remove('hidden');
        }
    </script>
</x-app-layout>
