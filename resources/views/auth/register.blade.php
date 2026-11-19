@extends('layout')
@section('title', 'Регистрация')
@section('content')
<div class="mx-auto mt-10 max-w-sm">
    <h1 class="mb-6 text-center text-2xl font-bold text-amber-300">Создать героя</h1>
    <form method="POST" action="{{ route('register') }}" class="card space-y-4">@csrf
        <div><label class="label">Имя</label><input name="name" value="{{ old('name') }}" class="input" required autofocus></div>
        <div><label class="label">Email</label><input name="email" type="email" value="{{ old('email') }}" class="input" required></div>
        <div><label class="label">Пароль</label><input name="password" type="password" class="input" required></div>
        <div><label class="label">Повтор пароля</label><input name="password_confirmation" type="password" class="input" required></div>
        <button class="btn btn-p w-full">Начать путь</button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-400">Уже есть герой? <a href="{{ route('login') }}" class="text-violet-400">Войти</a></p>
</div>
@endsection
