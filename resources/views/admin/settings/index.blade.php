<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            設定
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
            <!-- Profile Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">管理者基本情報</h3>
                    <p class="text-sm text-slate-500 mt-1">アカウントの基本情報とパスワードを更新できます。</p>
                </div>
                <div class="p-6">
                    @if (session('status') === 'profile-updated')
                        <div class="mb-4 bg-emerald-50 text-emerald-700 p-4 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            プロフィールが更新されました。
                        </div>
                    @endif

                    <form method="post" action="{{ route('admin.settings.update-profile') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="name" :value="__('名前')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('メールアドレス')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="border-t border-slate-100 pt-6 mt-6">
                            <h4 class="text-sm font-semibold text-slate-700 mb-4">パスワード変更（変更する場合のみ入力）</h4>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="password" :value="__('新しいパスワード')" />
                                    <x-text-input id="password" name="password" type="password"
                                        class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('パスワード確認')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation"
                                        type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>{{ __('保存する') }}</x-primary-button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</x-app-layout>