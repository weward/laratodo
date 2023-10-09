<?php

namespace App\Services;

use App\Jobs\RemoveFileFromStorageJob;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    public function saveFile($request, $taskId)
    {
        // Save and store new attachments
        if (!$request->hasFile('new_attachments')) {
            return;
        }

        try {
            $files = $request->file('new_attachments');

            foreach ($files as $file) {
                if ($filename = $this->storeFile($file)) {
                    // save to db
                    $details = [
                        'filename' => $filename,
                        'type' => $file->getClientMimeType(),
                        'task_id' => $taskId,
                    ];

                    $this->saveDetails($details);
                }
            }
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    public function updateAttachments($request, $task)
    {
        // request only returns array of attachment->ids not an object array
        $updatedAttachmentIds = $request?->attachments ?? [];

        $task->load(['attachments']);
        $oldAttachmentIds = $task->attachments?->pluck('id')->toArray();

        if (count($oldAttachmentIds) == count($updatedAttachmentIds)) {
            return;
        }
        // return only those to be removed (not present in updatedAttachments)
        $idsToRemove = array_diff($oldAttachmentIds, $updatedAttachmentIds);
        $toRemove = Attachment::whereIn('id', $idsToRemove)->get();
        $filenames = $toRemove->count() ? $toRemove->pluck('filename')->toArray() : [];

        // Delete from db
        if (count($idsToRemove)) {
            $this->deleteFromDb(($idsToRemove));
            // Delete actual file from storage
            RemoveFileFromStorageJob::dispatch($filenames);
        }

        return true;
    }

    public function saveDetails($attachmentDetails)
    {
        try {
            return Attachment::create($attachmentDetails);
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    public function storeFile($file)
    {
        $time = time();
        $filename = '';
        try {
            // save to storage
            $filename = $time . '_' . $file->getClientOriginalName();
            $filePath = env('UPLOAD_PATH') . '/' . $filename;

            Storage::disk('public')->put($filePath, file_get_contents($file));
        } catch (\Throwable $th) {
            info($th->getMessage());
            // remove from storage
            RemoveFileFromStorageJob::dispatch($time . '_' . $file->getClientOriginalName());
        }

        return $filename;
    }

    public function removeFile($filenames = [])
    {
        if (!$filenames || !count($filenames)) {
            return;
        }

        $filenames = (!is_array($filenames)) ? [$filenames] : $filenames;

        foreach ($filenames as $filename) {
            try {
                $filePath = env('UPLOAD_PATH') . '/' . $filename;

                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            } catch (\Throwable $th) {
                info($th->getMessage());
            }
        }

        return true;
    }

    public function deleteFromDb($attachmentIds)
    {
        if (!$attachmentIds) {
            return;
        }

        $attachmentIds = (!is_array($attachmentIds)) ? [$attachmentIds] : $attachmentIds;

        foreach ($attachmentIds as $attachmentId) {
            try {
                $attachment = Attachment::where('id', $attachmentId)->first();
                $attachment->delete();
            } catch (\Throwable $th) {
                info($th->getMessage());
            }
        }
    }

}
