<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            企業情報を編集
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.companies.update', $company) }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('企業名')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $company->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Business Description -->
                <div class="mt-6">
                    <x-input-label for="business_description" :value="__('事業内容')" />
                    <textarea id="business_description" name="business_description" rows="3"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">{{ old('business_description', $company->business_description) }}</textarea>
                    <x-input-error :messages="$errors->get('business_description')" class="mt-2" />
                </div>

                <!-- Teacher -->
                <div class="mt-6">
                    <x-input-label for="teacher_id" :value="__('担当講師')" />
                    <select id="teacher_id" name="teacher_id"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                        <option value="">担当講師を選択...</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $company->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">※企業1社につき担当講師は1名のみ割り当て可能です。</p>
                    <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                </div>

                <!-- Status -->
                <div class="mt-6">
                    <x-input-label for="status" :value="__('ステータス')" />
                    <select id="status" name="status"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                        <option value="free_trial" {{ old('status', $company->status) == 'free_trial' ? 'selected' : '' }}>無料研修中</option>
                        <option value="active" {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>研修中
                        </option>
                        <option value="finished" {{ old('status', $company->status) == 'finished' ? 'selected' : '' }}>修了済
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <!-- Contract Start Date -->
                <div class="mt-6">
                    <x-input-label for="contract_start_date" :value="__('研修開始日（有料契約時）')" />
                    <x-text-input id="contract_start_date" class="block mt-1 w-full" type="date"
                        name="contract_start_date" :value="old('contract_start_date', $company->contract_start_date ? $company->contract_start_date->format('Y-m-d') : '')" />
                    <p class="text-xs text-slate-500 mt-1">※無料研修中は入力不要です。</p>
                    <x-input-error :messages="$errors->get('contract_start_date')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('admin.companies.index') }}"
                        class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                    <x-primary-button>
                        {{ __('更新する') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>