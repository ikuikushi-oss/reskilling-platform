<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTG詳細 (管理者): {{ $meetingLog->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Basic Info -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">基本情報</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">ID / タイトル</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $meetingLog->id }} / {{ $meetingLog->title }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">企業</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $meetingLog->company->name ?? '不明' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">実施日時</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $meetingLog->started_at->format('Y-m-d H:i') }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">YouTube URL</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($meetingLog->youtube_url)
                                    <a href="{{ $meetingLog->youtube_url }}" target="_blank"
                                        class="text-indigo-600 hover:text-indigo-900">{{ $meetingLog->youtube_url }}</a>
                                @else
                                    <span class="text-gray-400">未登録</span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">メモ</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $meetingLog->memo ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Transcript Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-4xl">
                    <h3 class="text-lg font-medium text-slate-900 mb-2">文字起こし（講師・管理者限定）</h3>
                    <p class="text-sm text-gray-500 mb-4">YouTube Studioからダウンロードした字幕ファイル（.srt / .vtt）をアップロードしてください。</p>

                    <!-- Status Display -->
                    <div class="mb-6">
                        @if($meetingLog->transcript_status === 'not_uploaded')
                            <div class="p-4 bg-gray-50 text-gray-500 rounded-md border border-gray-200">
                                文字起こしはまだ登録されていません。
                            </div>
                        @elseif($meetingLog->transcript_status === 'failed')
                            <div class="p-4 bg-red-50 text-red-600 rounded-md border border-red-200">
                                字幕ファイルの読み込みに失敗しました。再度アップロードしてください。
                            </div>
                        @elseif($meetingLog->transcript_status === 'ready')
                            <textarea readonly
                                class="block w-full h-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50">{{ $meetingLog->transcript_text }}</textarea>
                            <div class="mt-2 text-right text-xs text-gray-500">
                                登録日時: {{ $meetingLog->transcript_uploaded_at->format('Y-m-d H:i:s') }}
                            </div>
                        @endif
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('admin.meeting-logs.transcript.upload', $meetingLog) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="transcript_file" value="字幕ファイル (.srt, .vtt)" />
                            <input type="file" name="transcript_file" id="transcript_file" accept=".srt,.vtt,.txt"
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            " />
                            <x-input-error :messages="$errors->get('transcript_file')" class="mt-2" />
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>
                                {{ $meetingLog->transcript_status === 'ready' ? '再アップロード' : 'アップロード' }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- AI Summary Section -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-slate-900">AI要約</h3>
                            @if($meetingLog->transcript_status === 'ready')
                                <form action="{{ route('admin.meeting-logs.summarize', $meetingLog) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        AIで要約を生成
                                    </button>
                                </form>
                            @endif
                        </div>

                        {{-- Admin Update Form --}}
                        <form action="{{ route('admin.meeting-logs.update', $meetingLog) }}" method="POST">
                            @csrf
                            <textarea name="transcript_summary" rows="10" placeholder="AI要約がここに生成されます。手動で編集も可能です。"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $meetingLog->transcript_summary }}</textarea>
                            
                            <div class="mt-2 flex justify-end">
                                <x-secondary-button type="submit">要約を保存</x-secondary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>