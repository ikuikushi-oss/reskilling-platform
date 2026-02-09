<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                講義ページ管理
            </h2>
            <a href="{{ route('admin.lecture-pages.create') }}"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                講義ページを追加
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                            順序</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                            サムネイル</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                            タイトル</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                            ステータス</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                            操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($lecturePages as $lecturePage)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                #{{ $lecturePage->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lecturePage->thumbnail_path)
                                    <img src="{{ asset($lecturePage->thumbnail_path) }}" alt="" class="h-12 w-20 object-cover rounded border border-slate-200">
                                @else
                                    <div class="h-12 w-20 bg-slate-100 rounded border border-slate-200 flex items-center justify-center text-slate-400 text-xs">
                                        No Image
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ $lecturePage->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($lecturePage->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        公開中
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">
                                        停止中
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.lecture-pages.edit', $lecturePage) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-4">編集</a>
                                <form action="{{ route('admin.lecture-pages.deactivate', $lecturePage) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('この講義を停止（非公開）にしますか？\n※ 削除はされませんが、生徒からは見えなくなります。');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-amber-600 hover:text-amber-900">停止</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 text-sm">
                                登録されている講義ページはありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>