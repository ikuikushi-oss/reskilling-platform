<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            運用状況
        </h2>
    </x-slot>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Teachers -->
        <a href="{{ route('admin.teachers.index') }}"
            class="block bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition group">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">講師数</span>
                </div>
                <div class="flex items-baseline">
                    <h3 class="text-3xl font-bold text-slate-900">{{ $teacherCount }}</h3>
                    <span class="ml-2 text-sm text-slate-500">名</span>
                </div>
            </div>
        </a>

        <!-- Total Companies (Trainings) -->
        <a href="{{ route('admin.companies.index') }}"
            class="block bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition group">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">研修総数</span>
                </div>
                <div class="flex items-baseline">
                    <h3 class="text-3xl font-bold text-slate-900">{{ $companyTotal }}</h3>
                    <span class="ml-2 text-sm text-slate-500">件</span>
                </div>
            </div>
        </a>

        <!-- Active Companies -->
        <a href="{{ route('admin.companies.index', ['status' => 'active']) }}"
            class="block bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition group">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">研修中</span>
                </div>
                <div class="flex items-baseline">
                    <h3 class="text-3xl font-bold text-slate-900">{{ $companyActive }}</h3>
                    <span class="ml-2 text-sm text-slate-500">社</span>
                </div>
            </div>
        </a>

        <!-- Finished Companies -->
        <a href="{{ route('admin.companies.index', ['status' => 'finished']) }}"
            class="block bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition group">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-slate-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">研修済み</span>
                </div>
                <div class="flex items-baseline">
                    <h3 class="text-3xl font-bold text-slate-900">{{ $companyFinished }}</h3>
                    <span class="ml-2 text-sm text-slate-500">社</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Scheduled MTG -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">本日のMTG予定（以降）</h3>
            <a href="{{ route('admin.meetings.index') }}" class="text-sm text-blue-600 hover:text-blue-800">すべて見る
                &rarr;</a>
        </div>
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                日時</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                企業 / トピック</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                状態</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($upcomingMeetings as $meeting)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                    {{ $meeting->scheduled_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                    <div class="font-medium text-slate-900">{{ $meeting->company->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ $meeting->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($meeting->scheduled_at->isPast())
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">終了</span>
                                    @elseif($meeting->scheduled_at->isToday())
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">本日</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">予定</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ $meeting->zoom_start_url }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-900 mr-3">参加 (Host)</a>
                                    {{-- Detail link if exists, currently we list all meetings in index --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-slate-500">予定されているMTGはありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent MTG Logs -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">直近のMTG履歴</h3>
            <a href="{{ route('admin.meeting-logs.index') }}" class="text-sm text-blue-600 hover:text-blue-800">すべて見る
                &rarr;</a>
        </div>
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                実施日時</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                企業 / トピック</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                主催者</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                詳細</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($recentLogs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                    {{ $log->started_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                    <div class="font-medium text-slate-900">{{ $log->company->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ $log->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    {{ $log->creator->name ?? $log->host_email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.meeting-logs.show', $log) }}"
                                        class="text-blue-600 hover:text-blue-900">詳細</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-slate-500">履歴はありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Message (Optional) -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 flex items-start">
        <svg class="w-6 h-6 text-blue-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="text-sm font-bold text-blue-900">管理者ダッシュボードへようこそ</h4>
            <p class="text-sm text-blue-800 mt-1">現在の運用状況が一目で確認できます。詳細なデータを確認・編集する場合は、各カードをクリックしてください。</p>
        </div>
    </div>
</x-app-layout>