<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTG実績エクスポート
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <form action="{{ route('admin.mtgs.export.csv') }}" method="GET" class="space-y-6">

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="date_from" value="期間 (開始)" />
                                <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from"
                                    :value="now()->startOfMonth()->format('Y-m-d')" required />
                            </div>
                            <div>
                                <x-input-label for="date_to" value="期間 (終了)" />
                                <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to"
                                    :value="now()->format('Y-m-d')" required />
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Company -->
                            <div>
                                <x-input-label for="company_id" value="企業" />
                                <select id="company_id" name="company_id"
                                    class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">全ての企業</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Teacher -->
                            <div>
                                <x-input-label for="teacher_id" value="講師" />
                                <select id="teacher_id" name="teacher_id"
                                    class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">全ての講師</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Student -->
                            <div>
                                <x-input-label for="student_id" value="生徒" />
                                <select id="student_id" name="student_id"
                                    class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">全ての生徒</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Type -->
                        <div>
                            <x-input-label for="type" value="CSV種別" />
                            <select id="type" name="type"
                                class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="summary">MTG実績サマリ</option>
                                <option value="participants">参加者明細</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between">
                            <!-- Sync Button (Left) -->
                            <div class="flex items-center">
                                <span class="text-sm text-slate-500 mr-2">Zoom実績同期 (直近30日)</span>
                                <!-- Use a separate form for the sync action since it's a POST -->
                            </div>

                            <x-primary-button>
                                CSVダウンロード
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Separate Sync Form -->
                    <form action="{{ route('admin.zoom.sync') }}" method="POST"
                        class="mt-4 border-t border-slate-200 pt-4">
                        @csrf
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">※ 直近30日間の未同期MTGをZoomから取得します。</span>
                            <x-secondary-button type="submit">
                                Zoom実績を同期する
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>