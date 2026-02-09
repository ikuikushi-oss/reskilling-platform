<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTG管理
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Bar -->
            <div class="flex justify-end mb-6">
                <a href="{{ route('teacher.meetings.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    MTGを作成
                </a>
            </div>

            <!-- Messages -->
            @if (session('success'))
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p class="font-bold">成功</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    
                    @if($meetings->isEmpty())
                        <div class="text-center py-10 text-slate-500">
                            担当企業のMTGはまだありません。
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">開催日時 (JST)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">タイトル</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">企業</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">参加人数</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">状態</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($meetings as $meeting)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                                {{ $meeting->scheduled_at->format('Y/m/d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                                {{ $meeting->title }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                {{ $meeting->company->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                @if($meeting instanceof \App\Models\MeetingLog)
                                                    {{ $meeting->students->count() }}名
                                                @else
                                                    {{ $meeting->participants->count() }}名
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($meeting->scheduled_at->isFuture())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        予定
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">
                                                        終了
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                @if($meeting instanceof \App\Models\MeetingLog)
                                                    {{-- Logs (Hub created) use Edit form for details --}}
                                                    <a href="{{ route('teacher.meeting-logs.edit', $meeting) }}" class="text-blue-600 hover:text-blue-900 font-medium">詳細・編集</a>
                                                @else
                                                    <a href="{{ route('teacher.meetings.show', $meeting) }}" class="text-blue-600 hover:text-blue-900 font-medium">詳細</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $meetings->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
