<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            提出レビュー
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <!-- Tabs/Filter (No cards, just visual separation) -->
        <div class="border-b border-slate-100 px-6 py-4 flex space-x-6">
            <a href="{{ route('teacher.submissions.index') }}"
                class="text-sm font-medium {{ !request('status') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-slate-500 hover:text-slate-700' }}">
                すべて
            </a>
            <a href="{{ route('teacher.submissions.index', ['status' => 'submitted']) }}"
                class="text-sm font-medium {{ request('status') == 'submitted' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-slate-500 hover:text-slate-700' }}">
                未レビュー
                @if($submissions->where('status', 'submitted')->count() > 0)
                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-600 text-xs">New</span>
                @endif
            </a>
            <a href="{{ route('teacher.submissions.index', ['status' => 'revision_required']) }}"
                class="text-sm font-medium {{ request('status') == 'revision_required' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-slate-500 hover:text-slate-700' }}">
                再提出待ち
            </a>
            <a href="{{ route('teacher.submissions.index', ['status' => 'passed']) }}"
                class="text-sm font-medium {{ request('status') == 'passed' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-slate-500 hover:text-slate-700' }}">
                合格
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            提出日時</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            生徒名 / 所属</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            課題（講義）</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            ステータス</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($submissions as $submission)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $submission->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $submission->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $submission->user->company->name ?? '未所属' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $submission->lecturePage->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission->status == 'submitted')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 border border-slate-200">提出済み（未レビュー）</span>
                                @elseif($submission->status == 'revision_required')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-100">再提出依頼</span>
                                @elseif($submission->status == 'passed')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">合格</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('teacher.submissions.show', $submission) }}"
                                    class="text-blue-600 hover:text-blue-900 font-semibold">レビューする</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-sm">
                                該当する提出はありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>