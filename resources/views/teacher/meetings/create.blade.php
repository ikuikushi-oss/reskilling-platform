<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            新規MTG作成
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">エラー</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <form method="POST" action="{{ route('teacher.meetings.store') }}">
                        @csrf

                        <!-- Company Selection -->
                        <div class="mb-4">
                            <x-input-label for="company_id" :value="__('対象企業')" />
                            <select id="company_id" name="company_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required onchange="filterStudents()">
                                <option value="">選択してください</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('MTGタイトル')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                :value="old('title')" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Date & Time -->
                        <div class="mb-4">
                            <x-input-label for="scheduled_at" :value="__('開催日時 (JST)')" />
                            <input id="scheduled_at" type="datetime-local" name="scheduled_at"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm cursor-pointer"
                                :value="old('scheduled_at', $defaultTime ?? '')" required
                                onclick="try{this.showPicker()}catch(e){}">
                            <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <x-input-label for="duration_minutes" :value="__('所要時間 (分)')" />
                            <select id="duration_minutes" name="duration_minutes"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="30">30分</option>
                                <option value="60" selected>60分</option>
                                <option value="90">90分</option>
                                <option value="120">120分</option>
                            </select>
                            <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                        </div>

                        <!-- Participants (Dynamic Checkboxes) -->
                        <div class="mb-6">
                            <x-input-label :value="__('参加生徒')" />
                            <div id="student-list"
                                class="mt-2 p-4 bg-slate-50 rounded-md border border-slate-200 min-h-[100px]">
                                <p class="text-slate-400 text-sm">企業を選択すると生徒リストが表示されます。</p>
                            </div>
                            <x-input-error :messages="$errors->get('participants')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-4">
                            <a href="{{ route('teacher.meetings.index') }}"
                                class="text-sm text-slate-600 hover:text-slate-900 underline">キャンセル</a>
                            <x-primary-button>
                                {{ __('MTGを作成') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JS
        const companiesData = @json($companies);

        function filterStudents() {
            const companyId = document.getElementById('company_id').value;
            const container = document.getElementById('student-list');

            if (!companyId) {
                container.innerHTML = '<p class="text-slate-400 text-sm">企業を選択すると生徒リストが表示されます。</p>';
                return;
            }

            const selectedCompany = companiesData.find(c => c.id == companyId);

            if (!selectedCompany || !selectedCompany.students || selectedCompany.students.length === 0) {
                container.innerHTML = '<p class="text-orange-500 text-sm">この企業には登録されている生徒がいません。</p>';
                return;
            }

            let html = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-2">';
            selectedCompany.students.forEach(student => {
                html += `
                    <label class="flex items-center space-x-2 p-2 rounded hover:bg-slate-100 cursor-pointer">
                        <input type="checkbox" name="participants[]" value="${student.id}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                        <span class="text-sm text-slate-700">${student.name}</span>
                    </label>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const scheduledAtInput = document.getElementById('scheduled_at');
            if (scheduledAtInput && !scheduledAtInput.value) {
                const now = new Date();
                now.setHours(now.getHours() + 1);
                now.setMinutes(0);

                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');

                scheduledAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });
    </script>
</x-app-layout>