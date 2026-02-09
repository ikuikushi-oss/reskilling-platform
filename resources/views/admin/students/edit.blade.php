<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            生徒情報を編集
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.students.update', $student) }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('生徒名')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $student->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-6">
                    <x-input-label for="email" :value="__('メールアドレス')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $student->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password (Optional) -->
                <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <h3 class="text-sm font-medium text-slate-700 mb-2">パスワードを変更する場合のみ入力</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="password" :value="__('新しいパスワード')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('新しいパスワード（確認）')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                name="password_confirmation" />
                        </div>
                    </div>
                </div>

                <!-- Company -->
                <div class="mt-6">
                    <x-input-label for="company_id" :value="__('所属企業')" />
                    <select id="company_id" name="company_id"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        required>
                        <option value="">選択してください</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $student->company_id == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('admin.students.index') }}"
                        class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                    <x-primary-button>
                        {{ __('更新する') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>