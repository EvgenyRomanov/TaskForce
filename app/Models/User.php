<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property $id
 * @property $role
 * @property $cnt_failed_tasks
 * @property $executorTasks
 * @property $name
 * @property $mobile
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'city_id',
        'mobile',
        'telegram',
        'about',
        'birth_date',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function executorTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'executor_id');
    }

    public function customerTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'customer_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class, 'executor_id');
    }

    public function executorFeedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'executor_id');
    }

    public function customerFeedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'customer_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, foreignPivotKey: 'executor_id');
    }

    public function categoryIds(): array
    {
        return $this->categories()->get()->map(function ($item) {
            return $item->id;
        })->toArray();
    }

    public function getCountFailedTasks(): int
    {
        $tasks = $this->executorTasks->filter(function (Task $task) {
            return $task->status->name == Status::FAILED;
        });

        return $tasks->count();
    }

    public function getCountDoneTasks(): int
    {
        $tasks = $this->executorTasks->filter(function (Task $task) {
            return $task->status->name == Status::DONE;
        });

        return $tasks->count();
    }

    public function rating(): float
    {
        $sumRating = 0;
        $cntFeedback = 0;

        foreach ($this->executorFeedbacks()->get() as $feedback) {
            $sumRating += $feedback->rating;
            $cntFeedback += 1;
        }

        if (($cntFeedback - $this->cnt_failed_tasks) === 0) {
            return 0;
        }

        return round($sumRating / ($cntFeedback + $this->cnt_failed_tasks), 2);
    }

    public function isCustomer(): bool
    {
        return $this->role->name === Role::CUSTOMER;
    }

    public function isExecutor(): bool
    {
        return $this->role->name === Role::EXECUTOR;
    }

    public function wasRespondToTask(int $taskId): bool
    {
        return $this->responses()->where('task_id', $taskId)->exists();
    }
}
