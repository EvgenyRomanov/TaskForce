<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    public const NEW = 'new';
    public const CANCELED = 'canceled';
    public const IN_PROGRESS = 'in_progress';
    public const DONE = 'done';
    public const FAILED = 'failed';

    protected $guarded = ['id'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
