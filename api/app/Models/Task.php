<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    const DEFAULT_PAGINATION_ROWS = 12;
    const CREATE_FAILED = 'Failed to save new task.';
    const UPDATE_FAILED = 'Failed to update task.';
    const DELETE_FAILED = 'Failed to delete task.';
    const STATUS_TOGGLE_FAILED = 'Failed to update task status.';

    protected $casts = [
        'tags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function scopeSearch($query, $value)
    {
        $query->where(function($q) use ($value) {
            $q->where('title', 'like', "%{$value}%");
            $q->orWhere('description', 'like', "%{$value}%");
        });
    }

    public function scopeTodo($query)
    {
        $query->whereNull('completed_at')
            ->whereNull('archived_at');
    }

    public function scopeCompleted($query)
    {
        $query->whereNotNull('completed_at')
            ->whereNull('archived_at');
    }
    public function scopeArchived($query)
    {
        $query->whereNotNull('archived_at');
    }

    public function scopeStatus($query, $status)
    {
        $query->when($status == 'todo', fn($q) => $q->todo())
        ->when($status == 'is_completed', fn($q) => $q->completed())
        ->when($status == 'is_archived', fn ($q) => $q->archived());
    }

    public function scopePriority($query, $value)
    {
        $query->where('priority_id', $value);
    }

    public function scopeDueDateRange($query, $from, $to)
    {
        $from = $from ? Carbon::parse($from)->startOfDay() : now()->subYears(1)->startOfDay();
        $to = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        $query->whereBetween('due_date', [$from, $to]);
    }

    public function scopeCompletedAtRange($query, $from, $to)
    {
        $from = $from ? Carbon::parse($from)->startOfDay() : now()->subYears(1)->startOfDay();
        $to = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        $query->whereBetween('completed_at', [$from, $to]);
    }

    public function scopeArchivedAtRange($query, $from, $to)
    {
        $from = $from ? Carbon::parse($from)->startOfDay() : now()->subYears(1)->startOfDay();
        $to = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        $query->whereBetween('archived_at', [$from, $to]);
    }

    public function scopeFilter($query, $request)
    {
        $params = cleanRequestQueryParams($request->all());
        $paramKeys = collect($params)->keys();

        $query->where('user_id', auth()->user()->id);

        if ($paramKeys->contains('search')) {
            $query->search($params->get('search'));
        }

        if ($paramKeys->contains('status')) {
            $query->status($params->get('status'));
        }

        if ($paramKeys->contains('priority_id')) {
            $query->priority($params->get('priority_id'));
        }

        if ($paramKeys->contains('due_date_from') || $paramKeys->contains('due_date_to')) {
            $query->dueDateRange($params->get('due_date_from'), $params->get('due_date_to'));
        }

        if ($paramKeys->contains('completed_at_from') || $paramKeys->contains('completed_at_to')) {
            $query->completedAtRange($params->get('completed_at_from'), $params->get('completed_at_to'));
        }

        if ($paramKeys->contains('archived_at_from') || $paramKeys->contains('archived_at_to')) {
            $query->archivedAtRange($params->get('archived_at_from'), $params->get('archived_at_to'));
        }

        $sortBy = ($paramKeys->contains('sort_by')) ? $params->get('sort_by') : 'id';
        $orderBy = ($paramKeys->contains('order_by')) ? $params->get('order_by') : 'desc';

        $query->orderBy($sortBy, $orderBy);

    }

}
