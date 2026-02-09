<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            提出履歴
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            提出日時</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            講義タイトル</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            判定結果</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            フィードバック</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach ($submissions as $submission)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <div class="flex flex-col">
                                    <span>提出: {{ $submission->created_at->format('Y-m-d H:i') }}</span>
                                    @if($submission->reviewed_at)
                                        <span class="text-xs text-slate-500 mt-1">判定:
                                            {{ $submission->reviewed_at->format('Y-m-d H:i') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ $submission->lecturePage->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission->status == 'submitted')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 border border-slate-200">審査中</span>
                                @elseif($submission->status == 'revision_required')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-100">再提出</span>
                                @elseif($submission->status == 'passed')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">合格</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs">
                                @if($submission->teacher_comment)
                                    <div class="bg-slate-50 p-2 rounded border border-slate-100">
                                        <p class="text-slate-800 text-xs whitespace-pre-wrap">{{ $submission->teacher_comment }}
                                        </p>
                                    </div>
                                    <div class="mt-1 text-right">
                                        <a href="{{ route('student.lectures.show', $submission->lecturePage) }}"
                                            class="text-xs text-blue-600 hover:underline">詳細・再提出</a>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>