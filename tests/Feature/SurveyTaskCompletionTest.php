<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserTask;
use App\Models\TaskTemplate;
use App\Models\Wallet;
use App\Models\GlobalSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyTaskCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $task;
    protected $template;

    protected function setUp(): void
    {
        parent::setUp();

        // Create global settings
        GlobalSetting::create([
            'app_name' => 'Test App',
            'task_validation_rules' => [
                'survey_min_time' => 60,
                'survey_max_time' => 600,
            ],
            'fraud_detection_rules' => [
                'max_tasks_per_hour' => 15,
            ],
        ]);

        // Create user
        $this->user = User::factory()->create([
            'status' => 'ACTIVE',
            'task_ban_until' => null,
        ]);

        // Create wallet
        Wallet::create([
            'user_id' => $this->user->id,
            'pending_balance' => 0,
            'available_balance' => 0,
            'total_earned' => 0,
        ]);

        // Create task template
        $this->template = TaskTemplate::create([
            'category' => 'SURVEY',
            'title' => 'Test Survey',
            'description' => 'Test Description',
            'questions' => [
                [
                    'id' => 1,
                    'text' => 'Question 1?',
                    'type' => 'single_choice',
                    'options' => ['Option A', 'Option B', 'Option C'],
                    'required' => true,
                ],
                [
                    'id' => 2,
                    'text' => 'Question 2?',
                    'type' => 'frequency',
                    'options' => ['Daily', 'Weekly', 'Monthly'],
                    'required' => true,
                ],
            ],
            'reward_amount' => 100,
            'completion_time_seconds' => 120,
            'min_completion_time' => 60,
            'is_active' => true,
        ]);

        // Create user task
        $this->task = UserTask::create([
            'user_id' => $this->user->id,
            'task_template_id' => $this->template->id,
            'status' => 'PENDING',
            'reward_amount' => 100,
            'assigned_at' => now(),
            'expires_at' => now()->addDay(),
        ]);
    }

    /** @test */
    public function user_can_complete_survey_task_successfully()
    {
        $response = $this->actingAs($this->user)->postJson("/tasks/{$this->task->id}/complete", [
            'response_data' => [
                'answers' => [
                    1 => 'Option A',
                    2 => 'Daily',
                ],
            ],
            'duration' => 65, // More than min 60 seconds
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->task->refresh();
        $this->assertEquals('COMPLETED', $this->task->status);
        $this->assertTrue($this->task->credited);

        $wallet = $this->user->wallet->fresh();
        $this->assertEquals(100, $wallet->pending_balance);
        $this->assertEquals(100, $wallet->total_earned);
    }

    /** @test */
    public function survey_fails_if_completed_too_fast()
    {
        $response = $this->actingAs($this->user)->postJson("/tasks/{$this->task->id}/complete", [
            'response_data' => [
                'answers' => [
                    1 => 'Option A',
                    2 => 'Daily',
                ],
            ],
            'duration' => 30, // Less than min 60 seconds
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->task->refresh();
        $this->assertNotEquals('COMPLETED', $this->task->status);
    }

    /** @test */
    public function survey_fails_if_user_is_banned()
    {
        $this->user->update(['task_ban_until' => now()->addHours(48)]);

        $response = $this->actingAs($this->user)->postJson("/tasks/{$this->task->id}/complete", [
            'response_data' => [
                'answers' => [
                    1 => 'Option A',
                    2 => 'Daily',
                ],
            ],
            'duration' => 65,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function survey_fails_if_already_completed()
    {
        $this->task->update(['status' => 'COMPLETED']);

        $response = $this->actingAs($this->user)->postJson("/tasks/{$this->task->id}/complete", [
            'response_data' => [
                'answers' => [
                    1 => 'Option A',
                    2 => 'Daily',
                ],
            ],
            'duration' => 65,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function survey_fails_if_expired()
    {
        $this->task->update(['expires_at' => now()->subHour()]);

        $response = $this->actingAs($this->user)->postJson("/tasks/{$this->task->id}/complete", [
            'response_data' => [
                'answers' => [
                    1 => 'Option A',
                    2 => 'Daily',
                ],
            ],
            'duration' => 65,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function survey_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->postJson("/tasks/{$this->task->id}/complete", [
            'duration' => 65,
            // Missing response_data
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['response_data']);
    }

    /** @test */
    public function user_can_start_task()
    {
        $response = $this->actingAs($this->user)->postJson("/tasks/{$this->task->id}/start");

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->task->refresh();
        $this->assertEquals('IN_PROGRESS', $this->task->status);
        $this->assertNotNull($this->task->started_at);
    }
}
