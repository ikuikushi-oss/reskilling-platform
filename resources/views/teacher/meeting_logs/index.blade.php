<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ $student->name }} - MTGログ一覧
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-slate-900">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">過去のMTGログ</h3>
                    <a href="{{ route('teacher.students.meeting-logs.create', $student) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        新規ログ作成
                    </a>
                </div>

                @if($logs->isEmpty())
                    <p class="text-slate-500 text-sm">MTGログはありません。</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        開始日時</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        タイトル</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        YouTube URL</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Zoom ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        メモ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            {{ $log->started_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                            {{ $log->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            <a href="{{ $log->youtube_url }}" target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900 underline">
                                                視聴する
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            {{ $log->zoom_meeting_id ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">
                                            {{ Str::limit($log->memo, 50) }}
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
</x-app-layout>