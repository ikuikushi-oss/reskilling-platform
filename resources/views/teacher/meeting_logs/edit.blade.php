<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTGログを編集
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
            <div class="p-6 text-slate-900">
                <form method="POST" action="{{ route('teacher.meeting-logs.update', $meetingLog) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" value="MTGタイトル" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                            :value="old('title', $meetingLog->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- YouTube URL -->
                    <div>
                        <x-input-label for="youtube_url" value="YouTube URL (録画)" />
                        <x-text-input id="youtube_url" class="block mt-1 w-full" type="url" name="youtube_url"
                            :value="old('youtube_url', $meetingLog->youtube_url)" placeholder="https://youtube.com/..." />
                        <p class="text-xs text-slate-500 mt-1">※ 録画URLが発行されたらここに入力してください。</p>
                        <x-input-error :messages="$errors->get('youtube_url')" class="mt-2" />
                    </div>

                    <!-- Memo -->
                    <div>
                        <x-input-label for="memo" value="メモ" />
                        <textarea id="memo" name="memo"
                            class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-32">{{ old('memo', $meetingLog->memo) }}</textarea>
                        <x-input-error :messages="$errors->get('memo')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        @if($meetingLog->students->isNotEmpty())
                            <a href="{{ route('teacher.students.mtgs', $meetingLog->students->first()) }}" class="text-slate-600 hover:text-slate-900 text-sm underline">
                                戻る
                            </a>
                        @else
                           <a href="{{ route('teacher.meeting-logs.index') }}" class="text-slate-600 hover:text-slate-900 text-sm underline">
                                戻る
                            </a>
                        @endif

                        <x-primary-button>
                            保存する
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
