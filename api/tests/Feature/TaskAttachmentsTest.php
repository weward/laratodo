<?php

namespace Tests\Feature;

use App\Jobs\RemoveFileFromStorageJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     * I want to attach multiple files (image, video, documents) to a task.
     */
    public function test_user_creates_task_with_attachments()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';

        Storage::fake('uploads');
        $file1 = UploadedFile::fake()->create('test_file.txt', 100);
        $file2 = UploadedFile::fake()->create('test_file.jpg', 100);
        $file3 = UploadedFile::fake()->create('test_file.doc', 100);


        $res = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'new_attachments' => [$file1, $file2, $file3],
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.title', $title)
            ->has('data.attachments', 3)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * I want to attach multiple files (image, video, documents) to a task.
     */
    public function test_user_creates_task_with_invalid_attachments()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';

        Storage::fake('uploads');

        // Invalid file type / extension
        $file1 = UploadedFile::fake()->create('test_file.dll', 100);

        $res = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'new_attachments' => [$file1],
        ]);

        $res->assertStatus(422);
        $res->assertInvalid(['new_attachments.0']);
    }

    /**
     * As an authenticated user,
     * I want to attach multiple files (image, video, documents) to a task
     */
    public function test_user_updates_task_with_attachments()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';

        $file1 = UploadedFile::fake()->create('test_file.txt', 100);
        $file2 = UploadedFile::fake()->create('test_file.jpg', 100);
        $file3 = UploadedFile::fake()->create('test_file.doc', 100);

        $task = Task::factory()->user(1)->create();

        $res = $this->postJson(route('tasks.update', ['task' => $task->id]), [
            '_method' => 'PUT',
            'title' => $title,
            'description' => 'This is a sample Description.',
            'new_attachments' => [$file1, $file2, $file3], // new attachment
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->has('data.attachments', 3)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * I want to attach multiple files (image, video, documents) to a task.
     */
    public function test_user_updates_task_with_invalid_attachments()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';

        // Invalid file type / extension
        $file1 = UploadedFile::fake()->create('test_file.dll', 100);

        $task = Task::factory()->user(1)->create();

        $res = $this->postJson(route('tasks.update', ['task' => $task->id]), [
            '_method' => 'PUT',
            'title' => $title,
            'description' => 'This is a sample Description.',
            'new_attachments' => [$file1], // new attachment
        ]);

        $res->assertStatus(422);
        $res->assertInvalid(['new_attachments.0']);
    }

    /**
     * As an authenticated user,
     * I want to attach multiple files (image, video, documents) to a task
     */
    public function test_user_updates_task_with_attachments_replace_existing_with_new_attachment()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';

        Storage::fake('uploads');
        $file1 = UploadedFile::fake()->create('test_file.txt', 100);
        $file2 = UploadedFile::fake()->create('test_file.jpg', 100);
        $file3 = UploadedFile::fake()->create('test_file.doc', 100);

        $newFile = UploadedFile::fake()->create('test_file.doc', 100);

        $res = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'new_attachments' => [$file1, $file2, $file3],
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->where('data.id', 1)
                ->has('data.attachments', 3)
                ->etc()
        );

        $res = $this->postJson(route('tasks.update', ['task' => 1]), [
            '_method' => 'PUT',
            'title' => $title,
            'description' => 'This is a sample Description.',
            'attachments' => [1, 2], // attachment ID -- will remove the existing attachments where id != 1 || 2
            'new_attachments' => [$newFile], // adds a new attachment
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->has('data.attachments', 3)
                ->etc()
        );
    }

}
