<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTGログ一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    
                    @if($logs->isEmpty())
                        <div class="text-center py-10 text-slate-500">
                            MTGログはまだありません。
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            <a href="{{ route('admin.meeting-logs.index', array_merge(request()->query(), ['sort' => 'started_at', 'direction' => request('sort') === 'started_at' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                                <span>実施日時</span>
                                                @if(request('sort') === 'started_at')
                                                    <span class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                                @else
                                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            <a href="{{ route('admin.meeting-logs.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                                <span>タイトル</span>
                                                @if(request('sort') === 'title')
                                                    <span class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                                @else
                                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            企業
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            作成者 (講師)
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            録画 (URL)
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            <a href="{{ route('admin.meeting-logs.index', array_merge(request()->query(), ['sort' => 'transcript_status', 'direction' => request('sort') === 'transcript_status' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                                <span>文字起こし</span>
                                                @if(request('sort') === 'transcript_status')
                                                    <span class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                                @else
                                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            操作
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($logs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                {{ $log->started_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                                {{ $log->title }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                {{ $log->company->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                {{ $log->creator->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                @if($log->youtube_url)
                                                    <a href="{{ $log->youtube_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                                        </svg>
                                                        視聴
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($log->transcript_status === 'ready')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        登録済
                                                    </span>
                                                @elseif($log->transcript_status === 'failed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        失敗
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        未登録
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.meeting-logs.show', $log) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    詳細 / アップロード
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $logs->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
