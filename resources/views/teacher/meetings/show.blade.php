<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTG詳細: {{ $meeting->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-medium text-slate-900 border-b pb-2 mb-4">基本情報</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">開催日時</dt>
                                    <dd class="mt-1 text-lg font-semibold text-slate-900">
                                        {{ $meeting->scheduled_at->format('Y年m月d日 H:i') }}
                                        ({{ $meeting->duration_minutes }}分)</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">対象企業</dt>
                                    <dd class="mt-1 text-slate-900">{{ $meeting->company->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">作成日</dt>
                                    <dd class="mt-1 text-sm text-slate-600">
                                        {{ $meeting->created_at->format('Y/m/d H:i') }}</dd>
                                </div>
                            </dl>

                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-slate-900 border-b pb-2 mb-4">Zoom接続情報</h3>

                                <div class="bg-blue-50 border border-blue-100 rounded-md p-4 mb-4">
                                    <p class="text-sm font-bold text-blue-800 mb-1">【講師用】ホストURL (開始用)</p>
                                    <div class="flex items-center gap-2">
                                        <input type="text" value="{{ $meeting->zoom_start_url }}"
                                            class="w-full text-sm border-gray-300 rounded bg-white text-slate-600"
                                            readonly onclick="this.select()">
                                        <a href="{{ $meeting->zoom_start_url }}" target="_blank"
                                            class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                                            開始
                                        </a>
                                    </div>
                                    <p class="text-xs text-blue-600 mt-2">※ このURLは講師専用です。生徒には共有しないでください。</p>
                                </div>

                                <div class="bg-slate-50 border border-slate-200 rounded-md p-4">
                                    <p class="text-sm font-bold text-slate-700 mb-1">【生徒用】参加URL</p>
                                    <input type="text" value="{{ $meeting->zoom_join_url }}"
                                        class="w-full text-sm border-gray-300 rounded bg-white text-slate-600" readonly
                                        onclick="this.select()">
                                    <p class="text-xs text-slate-500 mt-2">※ このURLは生徒のマイページに自動で表示されます。</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-slate-900 border-b pb-2 mb-4">参加予定生徒
                                ({{ $meeting->participants->count() }}名)</h3>
                            @if($meeting->participants->isEmpty())
                                <p class="text-slate-500">参加者は登録されていません。</p>
                            @else
                                <ul class="divide-y divide-slate-100 bg-slate-50 rounded-md border border-slate-200">
                                    @foreach($meeting->participants as $participant)
                                        <li class="p-3 flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 rounded-full bg-slate-300 flex items-center justify-center text-white font-bold text-xs">
                                                {{ substr($participant->student->name, 0, 1) }}
                                            </div>
                                            <span
                                                class="ml-3 text-sm font-medium text-slate-700">{{ $participant->student->name }}</span>
                                            <span
                                                class="ml-auto text-xs text-slate-400">{{ $participant->student->email }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-200 flex justify-between">
                        <a href="{{ route('teacher.meetings.index') }}"
                            class="text-slate-600 hover:text-slate-900 font-medium">← 一覧に戻る</a>

                        <form action="{{ route('teacher.meetings.destroy', $meeting) }}" method="POST"
                            onsubmit="return confirm('本当に削除しますか？\nZoom上のミーティングも削除されます。');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 font-bold text-sm bg-red-50 hover:bg-red-100 px-4 py-2 rounded">
                                MTGを削除する
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>