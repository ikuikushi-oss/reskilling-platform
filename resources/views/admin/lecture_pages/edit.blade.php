<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            講義ページを編集
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.lecture-pages.update', $lecturePage) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Status -->
                <div class="mb-4 flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ $lecturePage->is_active ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 block text-sm font-medium text-slate-700">
                        公開する (有効)
                    </label>
                </div>

                <!-- Thumbnail -->
                <div class="mb-6">
                    <x-input-label for="thumbnail" :value="__('サムネイル画像')" />

                    @if($lecturePage->thumbnail_path)
                        <div class="mb-2">
                            <img src="{{ asset($lecturePage->thumbnail_path) }}" alt="Current Thumbnail"
                                class="h-32 w-auto object-cover rounded shadow-md border border-slate-200">
                        </div>
                    @endif

                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*" class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100 mt-2" />
                    <p class="mt-1 text-xs text-slate-500">※ 新しい画像をアップロードすると上書きされます。</p>
                    <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                </div>

                <!-- Sort Order -->
                <div class="mt-6">
                    <x-input-label for="sort_order" :value="__('表示順（数値）')" />
                    <x-text-input id="sort_order" class="block mt-1 w-full" type="number" name="sort_order"
                        :value="old('sort_order', $lecturePage->sort_order)" required min="0" />
                    <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                </div>

                <!-- Title -->
                <div class="mt-6">
                    <x-input-label for="title" :value="__('タイトル')" />
                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $lecturePage->title)" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <x-input-label for="description" :value="__('講義内容')" />
                    <textarea id="description" name="description" rows="10"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('description', $lecturePage->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('admin.lecture-pages.index') }}"
                        class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                    <x-primary-button>
                        {{ __('更新する') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>