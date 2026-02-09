<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Craft Reskilling') }}</title>
    <title>{{ config('app.name', 'AI Craft Reskilling') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ time() }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-[240px] flex-shrink-0 bg-white border-r border-slate-200 flex flex-col">
            <!-- Logo -->
            <!-- Logo -->
            <div class="h-24 flex items-center px-6 border-b border-slate-200">
                <img src="{{ asset('logo.png') }}?v={{ time() }}" alt="AI Craft Reskilling"
                    class="w-auto h-auto max-h-20 max-w-full">
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                @php
                    $user = Auth::user();
                @endphp

                @if($user->isAdmin())
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                        icon="home">
                        ダッシュボード
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">管理</p>
                    </div>
                    <x-nav-link :href="route('admin.companies.index')" :active="request()->routeIs('admin.companies.*')"
                        icon="building">
                        企業管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')"
                        icon="users">
                        講師管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')"
                        icon="academic-cap">
                        生徒管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.lecture-pages.index')"
                        :active="request()->routeIs('admin.lecture-pages.*')" icon="book-open">
                        カリキュラム管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.meetings.index')" :active="request()->routeIs('admin.meetings.*')"
                        icon="video-camera">
                        MTG (Scheduled)
                    </x-nav-link>
                    <x-nav-link :href="route('admin.meeting-logs.index')"
                        :active="request()->routeIs('admin.meeting-logs.*')" icon="document-text">
                        MTGログ (実績)
                    </x-nav-link>

                    <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*') || request()->routeIs('admin.lecture-pages.*')" icon="cog">
                        設定
                    </x-nav-link>

                    <x-nav-link :href="route('admin.mtgs.export')" :active="request()->routeIs('admin.mtgs.export')"
                        icon="chart-bar">
                        レポート (CSV)
                    </x-nav-link>

                @elseif($user->isTeacher())
                    <x-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')"
                        icon="home">
                        ホーム
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.submissions.index', ['status' => 'submitted'])"
                        :active="request()->routeIs('teacher.submissions.*')" icon="check-circle">
                        提出レビュー
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">管理</p>
                    </div>
                    <x-nav-link :href="route('teacher.companies.index')" :active="request()->routeIs('teacher.companies.*')"
                        icon="building">
                        担当企業
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.students.index')" :active="request()->routeIs('teacher.students.*')"
                        icon="academic-cap">
                        担当生徒
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.meetings.index')" :active="request()->routeIs('teacher.meetings.*')"
                        icon="video-camera">
                        MTG (Zoom)
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.meeting-logs.create')"
                        :active="request()->routeIs('teacher.meeting-logs.*')" icon="document-text">
                        MTGログ作成
                    </x-nav-link>

                @elseif($user->isStudent())
                    <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')"
                        icon="book-open">
                        講義一覧
                    </x-nav-link>
                    <x-nav-link :href="route('student.meetings.index')" :active="request()->routeIs('student.meetings.*')"
                        icon="video-camera">
                        MTG
                    </x-nav-link>
                @endif
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate w-32">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-3 py-2 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-md">
                        ログアウト
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50">
            <!-- Minimal Header -->
            @if (isset($header))
                <header class="bg-white border-b border-slate-200 h-16 flex items-center">
                    <div class="w-full max-w-7xl mx-auto px-6 sm:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <div class="max-w-7xl mx-auto p-6 sm:p-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>