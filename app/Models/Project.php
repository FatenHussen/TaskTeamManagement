<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Project
 *
 * This model represents a project entity in the system.
 * Each project can have multiple tasks and users assigned to it.
 * Soft deletion is enabled to allow the project to be "deleted" without being removed from the database.
 *
 * @package App\Models
 */
class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relationship: A project has many tasks.
     * 
     * This defines a one-to-many relationship between a project and its tasks.
     * Each project can have multiple associated tasks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    /**
     * Relationship: A project belongs to many users.
     *
     * This defines a many-to-many relationship between projects and users.
     * The relationship is managed through a pivot table `project_user`.
     * Additional attributes like 'role', 'contribution_hours', and 'last_activity'
     * are stored in the pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role', 'contribution_hours', 'last_activity') // Additional pivot table columns
            ->withTimestamps(); // Timestamps for pivot table (created_at and updated_at)
    }

    /**
     * Get the latest task related to the project.
     *
     * This retrieves the most recently created task for the project.
     * It uses the `latestOfMany` relation to retrieve the task with the latest `created_at`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestTask(): HasOne
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }

    /**
     * Get the oldest task related to the project.
     *
     * This retrieves the oldest task related to the project based on the `created_at` column.
     * It uses the `oldestOfMany` relation to retrieve the task with the earliest `created_at`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function oldestTask(): HasOne
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }

    /**
     * Get the highest priority task.
     *
     * This retrieves the task with the highest priority ('high') based on a condition
     * and the latest `created_at`. It uses `ofMany` to select the task with the maximum `created_at`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function highestPriorityTask(): HasOne
    {
        return $this->hasOne(Task::class)
            ->where('priority', 'high')
            ->ofMany('created_at', 'max');
    }
}
