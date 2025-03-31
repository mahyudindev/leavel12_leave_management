<x-app-layout>
    {{-- Main Dashboard Content --}}
    @if (in_array(auth()->user()->role, ['hrd', 'manager']))
        <x-admin-sidebar />
        
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" onsubmit="return handleFormSubmit(event)">
                        @csrf
                        @method('PATCH')
                        
                        {{-- Profile Form --}}
                        <div class="grid grid-cols-1 gap-4">
                            {{-- Nama --}}
                            <div>
                                <label for="name" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Nama</label>
                                <input id="name" name="name" type="text" value="{{ auth()->user()->name }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @error('name')
                                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Email</label>
                                <input id="email" name="email" type="email" value="{{ auth()->user()->email }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @error('email')
                                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Tanggal Masuk (readonly) --}}
                            <div>
                                <label for="tanggal_masuk" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Tanggal Masuk</label>
                                <input id="tanggal_masuk" name="tanggal_masuk" type="text" 
                                    value="{{ \Carbon\Carbon::parse(auth()->user()->tanggal_masuk_kerja)->format('d-m-Y') }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                    readonly>
                            </div>

                            {{-- Tanggal Berakhir Kontrak (readonly) --}}
                            <div>
                                <label for="tanggal_akhir_kerja" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Tanggal Berakhir Kontrak</label>
                                <input id="tanggal_akhir_kerja" name="tanggal_akhir_kerja" type="text" 
                                    value="{{ auth()->user()->tanggal_akhir_kerja ? \Carbon\Carbon::parse(auth()->user()->tanggal_akhir_kerja)->format('d-m-Y') : 'Kontrak Tetap' }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                    readonly>
                            </div>

                            {{-- Jumlah Cuti (readonly) --}}
                            <div>
                                <label for="jumlah_cuti" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Jumlah Cuti</label>
                                <input id="jumlah_cuti" name="jumlah_cuti" type="text" value="{{ auth()->user()->jumlah_cuti }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500" readonly>
                            </div>

                            {{-- Departemen (readonly) --}}
                            <div>
                                <label for="departemen" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Departemen</label>
                                <input id="departemen" name="departemen" type="text" value="{{ auth()->user()->departemen->nama ?? 'Tidak Ada Departemen' }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500" readonly>
                            </div>

                            {{-- Jabatan (readonly) --}}
                            <div>
                                <label for="jabatan" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Jabatan</label>
                                <input id="jabatan" name="jabatan" type="text" value="{{ auth()->user()->jabatan->nama ?? 'Tidak Ada Jabatan' }}" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500" readonly>
                            </div>
                        </div>

                        

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-between mt-8">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan Perubahan
                            </button>

                            <a href="{{ route('admin.dashboard') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Kembali
                            </a>
                        </div>
                    </form>
                    {{-- Password Update Section --}}
                        <div class="mt-8">
                            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <div class="max-w-xl">
                                    @include('profile.partials.update-password-form')
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @else
        <div class="p-4 transition-all duration-300">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            <form method="POST" action="{{ route('profile.update') }}" onsubmit="return handleFormSubmit(event)">
                                @csrf
                                @method('PATCH')

                                {{-- Nama --}}
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                                    <input id="name" name="name" type="text" value="{{ auth()->user()->name }}" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                </div>

                                {{-- Email --}}
                                <div class="mt-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input id="email" name="email" type="email" value="{{ auth()->user()->email }}" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                </div>

                                {{-- Tanggal Masuk (readonly) --}}
                                <div class="mt-4">
                                <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Masuk</label>
                                <input id="tanggal_masuk" name="tanggal_masuk" type="text" 
                                    value="{{ \Carbon\Carbon::parse(auth()->user()->tanggal_masuk_kerja)->format('d-m-Y') }}" 
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-opacity-50 bg-gray-100 dark:bg-gray-600 dark:text-gray-300" 
                                    readonly>
                                </div>

                                    {{-- Tanggal Berakhir Kontrak (readonly) --}}
                                    <div class="mt-4">
                                        <label for="tanggal_akhir_kerja" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Berakhir Kontrak</label>
                                        <input id="tanggal_akhir_kerja" name="tanggal_akhir_kerja" type="text" 
                                            value="{{ auth()->user()->tanggal_akhir_kerja ? \Carbon\Carbon::parse(auth()->user()->tanggal_akhir_kerja)->format('d-m-Y') : 'Kontrak Tetap' }}" 
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-opacity-50 bg-gray-100 dark:bg-gray-600 dark:text-gray-300" 
                                            readonly>
                                    </div>


                                {{-- Jumlah Cuti (readonly) --}}
                                <div class="mt-4">
                                    <label for="jumlah_cuti" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Cuti</label>
                                    <input id="jumlah_cuti" name="jumlah_cuti" type="text" value="{{ auth()->user()->jumlah_cuti }}" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-opacity-50 bg-gray-100 dark:bg-gray-600 dark:text-gray-300" readonly>
                                </div>

                                {{-- Departemen (readonly) --}}
                                <div class="mt-4">
                                    <label for="departemen" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departemen</label>
                                    <input id="departemen" name="departemen" type="text" value="{{ auth()->user()->departemen->nama ?? 'Tidak Ada Departemen' }}" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-opacity-50 bg-gray-100 dark:bg-gray-600 dark:text-gray-300" readonly>
                                </div>

                                {{-- Jabatan (readonly) --}}
                                <div class="mt-4">
                                    <label for="jabatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
                                    <input id="jabatan" name="jabatan" type="text" value="{{ auth()->user()->jabatan->nama ?? 'Tidak Ada Jabatan' }}" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-opacity-50 bg-gray-100 dark:bg-gray-600 dark:text-gray-300" readonly>
                                </div>
                                 
                                {{-- Submit Button --}}
                                <div class="mt-6">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function handleFormSubmit(event) {
            event.preventDefault();
            
            // Check if this is the password form
            const isPasswordForm = event.target.action.includes('password.update');
            
            Swal.fire({
                title: isPasswordForm ? 'Perubahan Password' : 'Perubahan Profil',
                text: "Perubahan akan disimpan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        }

        // Check for success messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for success message
            const status = '{{ session('status') }}';
            if (status === 'password-updated') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Password telah diperbarui.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            // Check for error message
            const error = '{{ session('error') }}';
            if (error) {
                Swal.fire({
                    title: 'Gagal!',
                    text: error,
                    icon: 'error',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });

        function togglePasswordVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Update icon
            const svg = icon.querySelector('svg');
            if (type === 'text') {
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Handle form submission errors
        document.addEventListener('DOMContentLoaded', function() {
            const errorBag = document.querySelector('#updatePassword');
            if (errorBag) {
                const errors = @json($errors->updatePassword->all());
                if (errors.length > 0) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: errors[0],
                        icon: 'error',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            }
        });
    </script>
</x-app-layout>
