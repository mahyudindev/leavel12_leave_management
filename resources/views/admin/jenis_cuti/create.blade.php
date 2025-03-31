<x-app-layout>
    <x-admin-sidebar />

    <div class="p-4 sm:ml-64">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold text-center text-black dark:text-white mb-6">Tambah Jenis Cuti</h1>

            <form action="{{ route('admin.jenis_cuti.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="nama_cuti" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Nama Cuti:</label>
                    <input type="text" name="nama_cuti" id="nama_cuti" value="{{ old('nama_cuti') }}" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nama_cuti') border-red-500 @enderror">
                    @error('nama_cuti')
                        <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan
                    </button>
                    <a href="{{ route('admin.jenis_cuti.index') }}" class="text-blue-500 dark:text-blue-400 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
