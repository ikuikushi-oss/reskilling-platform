<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('student.dashboard') }}" class="text-slate-500 hover:text-slate-700 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ $lecturePage->title }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">

        <!-- Submission Area -->
        <div class="space-y-8">
            <!-- New Submission Form -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6 border-b border-slate-100 bg-blue-50">
                    <h3 class="text-lg font-bold text-blue-900">課題の提出</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        「{{ $lecturePage->title }}」の課題が完了したら、ファイルをアップロードして提出してください。
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('student.submissions.store', $lecturePage) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                提出ファイル選択 <span class="text-rose-600">*</span>
                            </label>
                            <input type="file" name="files[]" multiple required class="block w-full text-sm text-slate-500
                              file:mr-4 file:py-2.5 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100
                              transition
                            " />
                            <p class="text-xs text-slate-500 mt-2">※ 複数選択可（画像、PDF、Document等）</p>
                            <x-input-error :messages="$errors->get('files')" class="mt-2" />
                            <x-input-error :messages="$errors->get('files.*')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>
                                {{ __('課題提出') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- History -->
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4 px-2 border-l-4 border-blue-600">過去の提出履歴</h3>
                <div class="space-y-4">
                    @forelse($existingSubmissions as $submission)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-sm text-slate-500 font-medium">
                                        {{ $submission->created_at->format('Y年m月d日 H:i') }} 提出
                                    </p>
                                    <div class="mt-2 text-sm text-slate-700">
                                        添付ファイル数: {{ $submission->items->count() }}
                                    </div>
                                </div>
                                <div>
                                    @if($submission->status == 'submitted')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            提出済み（審査中）
                                        </span>
                                    @elseif($submission->status == 'revision_required')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            再提出が必要です
                                        </span>
                                    @elseif($submission->status == 'passed')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            合格！
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($submission->teacher_comment)
                                <div class="mt-4 bg-slate-50 p-4 rounded-lg border border-slate-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">講師からのフィードバック</p>
                                    <p class="text-sm text-slate-800">{{ $submission->teacher_comment }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-500 px-2">まだ提出履歴はありません。</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>