<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Task
 *
 * This model represents a task entity in the system. 
 * A task is assigned to a project and can be associated with a creator (user who created the task)
 * and an assignee (user assigned to complete the task).
 * Soft deletion is enabled, so tasks can be "deleted" without being removed from the database.
 *
 * @package App\Models
 */
class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * This allows mass assignment on the listed fields during task creation.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'created_by',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'notes'
    ];

    /**
     * Relationship: A task is created by a user (creator).
     *
     * This defines a one-to-many relationship where a user can create multiple tasks.
     * This method fetches the user who created the task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: A task is assigned to a user (assignee).
     *
     * This defines a one-to-many relationship where a user can be assigned multiple tasks.
     * This method fetches the user to whom the task is assigned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship: A task belongs to a project.
     *
     * This defines a one-to-many relationship between a task and a project.
     * Each task is associated with a project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Scope a query to only include tasks with a specific status.
     *
     * This scope allows querying tasks by their status (e.g., new, in progress, completed).
     * Example usage: Task::status('completed')->get();
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include tasks with a specific priority.
     *
     * This scope allows querying tasks by their priority (e.g., low, medium, high).
     * Example usage: Task::priority('high')->get();
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $priority
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePriority(Builder $query, $priority): Builder
    {
        return $query->where('priority', $priority);
    }
}
