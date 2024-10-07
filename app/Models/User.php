<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $guarded = ['is_admin'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by', 'id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to', 'id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('role', 'contribution_hours', 'last_activity')
            ->withTimestamps();
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(
            Task::class,
            ProjectUser::class,
            'user_id',
            'project_id',
            'id',
            'project_id'
        );
    }

    public function getRoleForProject($projectId)
    {
        $project = $this->projects()->where('project_id', $projectId)->first();

        if (!$project) {
            throw new HttpResponseException($this->errorResponse(null, 'User is not part of this project.', 404));
        }

        return $project->pivot->role;
    }

    public function scopeFilterTasks($query, $status = null, $priority = null)
    {
        return $query->whereRelation('assignedTasks', function ($taskQuery) use ($status, $priority) {
            if (!empty($status)) {
                $taskQuery->where('status', $status);
            }
            if (!empty($priority)) {
                $taskQuery->where('priority', $priority);
            }
        });
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
