<?php

namespace Tests\Feature\Attachment;

use App\Exceptions\InvalidWeightAgeException;
use App\Mail\AppraisalPendingReviewMail;
use App\Mail\AppraisalRecordPendingReviewMail;
use App\Models\AppraisalRecord;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AppraisalRecordAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PositionSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            SeasonSeeder::class,
        ]);
    }

    public function test_public_user_cannot_access_adding_attachment_for_appraisal_record(): void
    {
        $response = $this->postJson('api/v1/appraisal-records/1/attachments');

        $response->assertStatus(401);
    }

    public function test_non_approver_cannot_access_adding_attachment_for_appraisal_record(): void
    {
        $appraisalRecord = $this->createUnsubmittedAppraisalRecord();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/attachments", [
            'file' => [
                UploadedFile::fake()->create('test.pdf'),
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_appraiser_can_add_attachment_for_appraisal_record(): void
    {
        $appraisalRecord = $this->createUnsubmittedAppraisalRecord();
        $appraiser = $appraisalRecord->appraiser;

        $response = $this->actingAs($appraiser)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/attachments", [
            'file' => [
                UploadedFile::fake()->create('test.pdf'),
                UploadedFile::fake()->create('test2.pdf'),
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_attachment_is_stored_correctly_after_adding_attachment_for_appraisal_record(): void
    {
        Storage::fake('attachment');

        $appraisalRecord = $this->createUnsubmittedAppraisalRecord();
        $appraiser = $appraisalRecord->appraiser;

        $response = $this->actingAs($appraiser)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/attachments", [
            'file' => [
                UploadedFile::fake()->create('test.pdf'),
                UploadedFile::fake()->create('test2.pdf'),
            ],
        ]);

        $response->assertStatus(200);
        
        $mediaItems = $appraisalRecord->getMedia('attachment');
        $this->assertEquals(2, $mediaItems->count());

        Storage::disk('attachment')->assertExists($mediaItems[0]->id . '/' . $mediaItems[0]->file_name);
        Storage::disk('attachment')->assertExists($mediaItems[1]->id . '/' . $mediaItems[1]->file_name);
    }
}
