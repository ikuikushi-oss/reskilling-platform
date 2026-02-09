<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ $student->name }} - MTGハブ
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- 1. Create Form Section -->
        <div id="create" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
            <div class="p-6 text-slate-900">
                <h3 class="text-lg font-bold mb-4">新規MTG作成</h3>

                <form method="POST" action="{{ route('teacher.meeting-logs.store') }}" class="space-y-4">
                    @csrf
                    <!-- Hidden: Company ID (Fixed to student's company) -->
                    <input type="hidden" name="company_id" value="{{ $student->company_id }}">

                    <!-- Students Selection (Checkbox) -->
                    <div>
                        <x-input-label value="参加生徒" />
                        <div class="mt-2 space-y-2 border p-4 rounded-md bg-slate-50 max-h-40 overflow-y-auto">
                            @foreach($student->company->students as $s)
                                <div class="flex items-center">
                                    <input type="checkbox" id="student_{{ $s->id }}" name="students[]" value="{{ $s->id }}"
                                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 disabled:opacity-50"
                                        {{-- Always checked and disabled for the target student --}}
                                        @if($s->id == $student->id) checked disabled @endif>
                                    <label for="student_{{ $s->id }}" class="ml-2 text-sm text-slate-600">
                                        {{ $s->name }}
                                        @if($s->id == $student->id) <span
                                        class="text-xs text-indigo-600 font-bold">(対象)</span> @endif
                                    </label>
                                    {{-- Hidden input for the target student to ensure submission even if disabled --}}
                                    @if($s->id == $student->id)
                                        <input type="hidden" name="students[]" value="{{ $s->id }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('students')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Title -->
                        <div>
                            <x-input-label for="title" value="MTGタイトル" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                :value="old('title')" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Started At -->
                        <div>
                            <x-input-label for="started_at" value="開始日時" />
                            <x-text-input id="started_at" class="block mt-1 w-full cursor-pointer" type="datetime-local"
                                name="started_at" :value="old('started_at')" required onclick="try{this.showPicker()}catch(e){}" />
                            <x-input-error :messages="$errors->get('started_at')" class="mt-2" />
                        </div>
                    </div>


                    <!-- Zoom Meeting ID (Optional) -->
                    <div>
                        <x-input-label for="zoom_meeting_id" value="Zoom ID (任意)" />
                        <x-text-input id="zoom_meeting_id" class="block mt-1 w-full" type="text" name="zoom_meeting_id"
                            :value="old('zoom_meeting_id')" />
                        <x-input-error :messages="$errors->get('zoom_meeting_id')" class="mt-2" />
                    </div>

            <!-- Memo -->
            <div class="mt-4">
                <x-input-label for="memo" value="メモ (任意)" />
                <textarea id="memo" name="memo"
                    class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-20">{{ old('memo') }}</textarea>
                <x-input-error :messages="$errors->get('memo')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    保存して一覧に追加
                </x-primary-button>
            </div>
            </form>
        </div>
    </div>

    <!-- 2. Log List Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
        <div class="p-6 text-slate-900">
            <h3 class="text-lg font-bold mb-4">過去のMTGログ</h3>

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
                                    参加生徒</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Zoom参加</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        録画(YouTube)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        メモ</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        操作</th>
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
                                        <td class="px-6 py-4 text-sm text-slate-600">
                                            {{ $log->students->pluck('name')->join(', ') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            @if($log->zoom_join_url)
                                                <a href="{{ $log->zoom_join_url }}" target="_blank"
                                                    class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                    参加する
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                                    未発行
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            @if($log->youtube_url)
                                                <a href="{{ $log->youtube_url }}" target="_blank"
                                                    class="text-indigo-600 hover:text-indigo-900 underline flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                                    </svg>
                                                    視聴する
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-xs">未登録</span>
                                            @endif
                                        </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ Str::limit($log->memo, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('teacher.meeting-logs.show', $log) }}"
                                            class="text-indigo-600 hover:text-indigo-900 text-xs font-bold mr-3">
                                            詳細
                                        </a>
                                        @if(!$log->youtube_url)
                                            <a href="{{ route('teacher.meeting-logs.edit', $log) }}"
                                                class="inline-flex items-center px-2 py-1 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 transition ease-in-out duration-150">
                                                URL登録
                                            </a>
                                        @else
                                            <a href="{{ route('teacher.meeting-logs.edit', $log) }}"
                                                class="text-slate-400 hover:text-slate-600 text-xs underline">
                                                編集
                                            </a>
                                        @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to force scroll to top
        const forceScrollTop = () => {
            const main = document.querySelector('main');
            if (main) main.scrollTop = 0;
            window.scrollTo(0, 0);
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        };

        // 1. Disable browser scroll restoration
        if ('scrollRestoration' in history) history.scrollRestoration = 'manual';

        // 2. Initial scroll fix
        forceScrollTop();

        // 3. Remove hash
        if (window.location.hash) {
            history.replaceState(null, null, window.location.pathname + window.location.search);
        }

        // 4. Time Default: Next Hour 00:00 (Client Side)
        const startedAtInput = document.getElementById('started_at');
        // Only set if value is empty (to respect old() input)
        // Since we removed :value from PHP for initial load, this JS will responsibly fill it.
        // Wait, if validation fails, Laravel fills the input via 'value' attribute if we use :value="old(...)".
        // I removed :value in previous step, which means old() won't work! I must fix that.
        
        // RE-INSERTING logic for old() via JS or checking attribute?
        // Actually, if I removed :value, I broke old() restoration.
        // I should have kept :value="old('started_at')" but NOT $defaultTime.
        // Let's assume I will fix the previous step to include :value="old('started_at')"
        
        if (startedAtInput && !startedAtInput.value) {
            const now = new Date();
            now.setHours(now.getHours() + 1);
            now.setMinutes(0);
            
            // Format to YYYY-MM-DDTHH:MM local time
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            startedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // 5. Repeated attempts for scroll
        ['load', 'pageshow', 'popstate'].forEach(evt => {
            window.addEventListener(evt, () => {
                forceScrollTop();
                setTimeout(forceScrollTop, 0);
                setTimeout(forceScrollTop, 10);
                setTimeout(forceScrollTop, 100);
            });
        });
        
        setTimeout(forceScrollTop, 10);
        setTimeout(forceScrollTop, 50);
        setTimeout(forceScrollTop, 200);
    });
</script>