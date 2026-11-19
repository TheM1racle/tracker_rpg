<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Habit extends Model
{
    protected $fillable = ['title', 'type', 'difficulty', 'days'];

    protected $casts = ['days' => 'array', 'difficulty' => 'integer'];

    public function user() { return $this->belongsTo(User::class); }

    public function logs() { return $this->hasMany(HabitLog::class); }

    public function isScheduled(string $date): bool
    {
        return in_array(Carbon::parse($date)->dayOfWeekIso, $this->days ?: range(1, 7));
    }
}
