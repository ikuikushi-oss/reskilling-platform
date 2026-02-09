<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            講義一覧（カリキュラム）
        </h2>
    </x-slot>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
        @forelse ($lecturePages as $page)
            <div class="bg-white overflow-hidden shadow-sm rounded-md border border-slate-200 flex flex-col hover:shadow-md transition duration-200 group cursor-pointer"
                onclick="window.location='{{ route('student.lectures.show', $page) }}'">

                <!-- Thumbnail -->
                <div class="w-full h-24 bg-slate-100 relative overflow-hidden">
                    @if($page->thumbnail_path)
                        <img src="{{ asset($page->thumbnail_path) }}" alt="{{ $page->title }}"
                            class="w-full h-full object-contain bg-slate-200 group-hover:scale-105 transition duration-300">
                    @else
                        <div class="flex items-center justify-center h-full text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    @endif
                    <!-- Badge -->
                    <div
                        class="absolute top-1 left-1 bg-white/90 backdrop-blur-sm px-1.5 py-0.5 rounded text-[10px] font-bold text-slate-700 shadow-sm">
                        第{{ str_pad($page->sort_order, 2, '0', STR_PAD_LEFT) }}回
                    </div>
                </div>

                <!-- Content -->
                <div class="p-2 flex-1 flex flex-col">
                    <h3
                        class="font-bold text-xs text-slate-800 mb-1 line-clamp-2 group-hover:text-blue-600 transition leading-tight">
                        {{ $page->title }}
                    </h3>
                    <p class="text-[10px] text-slate-500 mb-1 line-clamp-2 leading-tight">
                        {{ $page->description }}
                    </p>

                    <div class="mt-auto pt-1.5 border-t border-slate-100 flex items-center justify-end">
                        <span
                            class="inline-flex items-center text-[10px] font-semibold text-blue-600 group-hover:text-blue-800 transition">
                            課題を提出する
                            <svg class="ml-0.5 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-5">
                <div class="bg-white rounded-md shadow-sm p-8 text-center border border-slate-200">
                    <p class="text-sm text-slate-500 mb-2">現在公開されている講義はありません。</p>
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>