@extends('layout')
@section('title', 'Вход')
@section('content')
<div class="mx-auto mt-10 max-w-sm">
    <h1 class="mb-6 text-center text-2xl font-bold text-amber-300">⚔️ HabitQuest</h1>
    <form method="POST" action="{{ route('login') }}" class="card space-y-4">@csrf
        <div><label class="label">Email</label><input name="email" type="email" value="{{ old('email') }}" class="input" required autofocus></div>
        <div><label class="label">Пароль</label><input name="password" type="password" class="input" required></div>
        <label class="flex items-center gap-2 text-sm text-slate-400"><input type="checkbox" name="remember"> Запомнить</label>
        <button class="btn btn-p w-full">Войти</button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-400">Нет героя? <a href="{{ route('register') }}" class="text-violet-400">Создать</a></p>
</div>
@endsection
