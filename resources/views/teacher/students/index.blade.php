<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            担当生徒一覧
        </h2>
    </x-slot>

    <!-- Search & Filter -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('teacher.students.index') }}"
            class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <x-input-label for="search" :value="__('生徒名検索')" />
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')"
                    placeholder="生徒名を入力..." />
            </div>
            <div class="w-full md:w-48">
                <x-input-label for="company_id" :value="__('所属企業')" />
                <select id="company_id" name="company_id"
                    class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <x-primary-button>
                    {{ __('検索') }}
                </x-primary-button>
                <a href="{{ route('teacher.students.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    リセット
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>氏名</span>
                                @if(request('sort') === 'name')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>メールアドレス</span>
                                @if(request('sort') === 'email')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['sort' => 'company_name', 'direction' => request('sort') === 'company_name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>所属企業</span>
                                @if(request('sort') === 'company_name')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $student->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $student->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                @if($student->company)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $student->company->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">未所属</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('teacher.students.mtgs', $student) }}#create"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md text-xs font-semibold transition-colors duration-200 gap-1 border border-indigo-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    MTG
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500 text-sm">
                                条件に一致する生徒はありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-4 py-3 border-t border-slate-200">
            {{ $students->links() }}
        </div>
    </div>
</x-app-layout>