<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Event Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="title" :value="__('Judul Event')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus placeholder="Contoh: Seminar Nasional AI 2025" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="banner" :value="__('Poster / Banner Event')" />
                        <input type="file" id="banner" name="banner" class="block mt-1 w-full border border-gray-300 rounded-md p-2" required>
                        <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Max: 2MB.</p>
                        <x-input-error :messages="$errors->get('banner')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Lengkap')" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required placeholder="Jelaskan detail acara di sini...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="event_date" :value="__('Tanggal & Jam')" />
                            <x-text-input id="event_date" class="block mt-1 w-full" type="datetime-local" name="event_date" :value="old('event_date')" required />
                            <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="quota" :value="__('Kuota Peserta')" />
                            <x-text-input id="quota" class="block mt-1 w-full" type="number" name="quota" :value="old('quota')" required min="1" placeholder="Misal: 100" />
                            <x-input-error :messages="$errors->get('quota')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="location" :value="__('Lokasi Acara')" />
                        <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" required placeholder="Contoh: Aula Gedung B, Lantai 3" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button>
                            {{ __('Terbitkan Event') }}
                        </x-primary-button>
                    </div>
                </form>
                </div>
        </div>
    </div>
</x-app-layout>