<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $appends = ['fileUrl'];

    protected $guarded = [];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getFileUrlAttribute()
    {
        $filePath = env('UPLOAD_PATH') . '/' . $this->filename;
        $fileUrl = Storage::disk('public')->url($filePath);

        return $fileUrl;
    }

}
