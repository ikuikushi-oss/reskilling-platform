<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTGログ作成
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-slate-900">
                <form method="POST" action="{{ route('teacher.meeting-logs.store') }}" class="space-y-6" x-data="{
                        selectedCompany: '{{ old('company_id', $selectedCompanyId ?? '') }}',
                        companies: {{ Js::from($assignedCompanies) }}
                    }">
                    @csrf

                    <!-- Company Selection -->
                    <div>
                        <x-input-label for="company_id" value="企業" />
                        @if ($selectedCompanyId)
                            <div class="mt-1 text-slate-700 font-medium">
                                {{ $assignedCompanies->firstWhere('id', $selectedCompanyId)->name ?? '不明な企業' }}
                            </div>
                            <input type="hidden" name="company_id" value="{{ $selectedCompanyId }}">
                        @else
                            <select id="company_id" name="company_id" x-model="selectedCompany"
                                class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">選択してください</option>
                                <template x-for="company in companies" :key="company.id">
                                    <option :value="company.id" x-text="company.name"
                                        :selected="company.id == selectedCompany"></option>
                                </template>
                            </select>
                        @endif
                        <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                    </div>

                    <!-- Students Selection -->
                    <div>
                        <x-input-label for="students" value="参加生徒" />
                        <div class="mt-2 space-y-2 border p-4 rounded-md bg-slate-50 max-h-60 overflow-y-auto">
                            <template x-for="company in companies" :key="company.id">
                                <div x-show="company.id == selectedCompany">
                                    <template x-for="student in company.students" :key="student.id">
                                        <div class="flex items-center">
                                            <input type="checkbox" :id="'student_' + student.id" name="students[]"
                                                :value="student.id"
                                                class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                {{-- Pre-select if it's the target student --}} :checked="student.id == {{ $selectedStudent?->id ?? 'null' }} || {{ Js::from(old('students', [])) }}
                                                    .includes(String(student.id))">
                                            <label :for="'student_' + student.id" class="ml-2 text-sm text-slate-600"
                                                x-text="student.name"></label>
                                        </div>
                                    </template>
                                    <div x-show="!company.students.length" class="text-sm text-slate-500">
                                        この企業に所属する生徒はいません。
                                    </div>
                                </div>
                            </template>
                            <div x-show="!selectedCompany" class="text-sm text-slate-500">
                                企業を選択すると生徒一覧が表示されます。
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('students')" class="mt-2" />
                    </div>

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
                        <x-text-input id="started_at" class="block mt-1 w-full cursor-pointer" type="datetime-local" name="started_at"
                            :value="old('started_at')" required onclick="try{this.showPicker()}catch(e){}" />
                        <x-input-error :messages="$errors->get('started_at')" class="mt-2" />
                    </div>

                    <!-- Youtube URL -->
                    <div>
                        <x-input-label for="youtube_url" value="YouTube URL" />
                        <x-text-input id="youtube_url" class="block mt-1 w-full" type="url" name="youtube_url"
                            :value="old('youtube_url')" required placeholder="https://youtube.com/..." />
                        <x-input-error :messages="$errors->get('youtube_url')" class="mt-2" />
                    </div>

                    <!-- Zoom Meeting ID (Optional) -->
                    <div>
                        <x-input-label for="zoom_meeting_id" value="ZoomミーティングID (任意)" />
                        <x-text-input id="zoom_meeting_id" class="block mt-1 w-full" type="text" name="zoom_meeting_id"
                            :value="old('zoom_meeting_id')" />
                        <x-input-error :messages="$errors->get('zoom_meeting_id')" class="mt-2" />
                    </div>

                    <!-- Memo -->
                    <div>
                        <x-input-label for="memo" value="メモ (任意)" />
                        <textarea id="memo" name="memo"
                            class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-32">{{ old('memo') }}</textarea>
                        <x-input-error :messages="$errors->get('memo')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            保存
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </form>
    </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startedAtInput = document.getElementById('started_at');
            if (startedAtInput && !startedAtInput.value) {
                const now = new Date();
                now.setHours(now.getHours() + 1);
                now.setMinutes(0);

                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');

                startedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });
    </script>
</x-app-layout>