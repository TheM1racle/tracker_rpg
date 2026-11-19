<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HabitQuest')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#12101a] text-slate-200 antialiased">
@auth
    @php $me = auth()->user(); @endphp
    <header class="border-b border-white/5 bg-[#171422]">
        <div class="mx-auto flex max-w-3xl items-center gap-4 overflow-x-auto px-4 py-3 text-sm">
            <a href="{{ route('home') }}" class="font-bold text-amber-300">⚔️ HabitQuest</a>
            <a href="{{ route('home') }}" class="hover:text-white">Сегодня</a>
            <a href="{{ route('habits.index') }}" class="hover:text-white">Квесты</a>
            <a href="{{ route('shop') }}" class="hover:text-white">Магазин</a>
            <a href="{{ route('stats') }}" class="hover:text-white">Статистика</a>
            <span class="ml-auto whitespace-nowrap text-amber-300">{{ $me->gold }} 🪙</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-slate-500 hover:text-rose-400">Выйти</button></form>
        </div>
    </header>
    <div class="mx-auto max-w-3xl px-4 pt-4">
        <div class="card flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-violet-600/20 text-lg font-bold text-violet-300">{{ $me->level }}</div>
            <div class="flex-1 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="font-semibold">{{ $me->name }}</span>
                    <span class="text-slate-400">🛡️ {{ $me->shields }}</span>
                </div>
                <x-bar label="HP" :value="$me->hp" :max="\App\Support\Game::hpMax($me->level)" color="bg-rose-500" />
                <x-bar label="XP" :value="$me->xp" :max="\App\Support\Game::xpNeed($me->level)" color="bg-violet-500" />
            </div>
        </div>
    </div>
@endauth

<main class="mx-auto max-w-3xl px-4 pb-20 pt-4">
    @if (session('status'))
        <div class="toast mb-4 rounded-xl border border-violet-500/30 bg-violet-600/15 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-500/30 bg-rose-600/15 px-4 py-3 text-sm">{{ $errors->first() }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
