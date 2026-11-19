@php $h = $h ?? null; $days = $h->days ?? range(1, 7); @endphp
<div><label class="label">Название</label><input name="title" value="{{ $h->title ?? '' }}" class="input" required maxlength="80"></div>
<div class="grid grid-cols-2 gap-3">
    <div><label class="label">Тип</label>
        <select name="type" class="input">
            <option value="daily" @selected(($h->type ?? 'daily') === 'daily')>Дейлик (урон за пропуск)</option>
            <option value="habit" @selected(($h->type ?? '') === 'habit')>Привычка (+ / −)</option>
        </select>
    </div>
    <div><label class="label">Сложность</label>
        <select name="difficulty" class="input">
            @foreach ([1 => 'Лёгкая', 2 => 'Средняя', 3 => 'Тяжёлая'] as $k => $v)
                <option value="{{ $k }}" @selected(($h->difficulty ?? 2) === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
</div>
<div>
    <label class="label">Дни (для дейлика)</label>
    <div class="flex gap-1">
        @foreach (['Пн','Вт','Ср','Чт','Пт','Сб','Вс'] as $i => $name)
            <label class="flex-1 cursor-pointer rounded-lg bg-black/30 py-2 text-center text-xs has-checked:bg-violet-600">
                <input type="checkbox" name="days[]" value="{{ $i + 1 }}" class="hidden" @checked(in_array($i + 1, $days))>{{ $name }}
            </label>
        @endforeach
    </div>
</div>
