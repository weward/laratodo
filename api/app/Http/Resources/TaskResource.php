<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->load([
            'priority' => fn ($q) => $q->select('id', 'name'),
            'attachments' => fn ($q) => $q->select('id', 'task_id', 'filename', 'type')
        ]);

        $completedAt = $this->formatDate($this->completed_at);
        $archivedAt = $this->formatDate($this->archived_at);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->formatDate($this->due_date),
            'tags' => $this->tags ? $this->tags : [],
            'is_completed' => $completedAt ? 1 : 0,
            'is_archived' => $archivedAt ? 1 : 0,
            'completed_at' => $completedAt,
            'archived_at' => $archivedAt,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
            'user_id' => $this->user_id,
            'priority_id' => $this->priority_id,
            'priority' => $this->priority ?? [],
            'attachments' => $this->attachments ?? [],
        ];
    }

    public function formatDate($date)
    {
        return $date
            ? Carbon::parse($date)->copy()->format('Y-m-d')
            : '';
    }
}
