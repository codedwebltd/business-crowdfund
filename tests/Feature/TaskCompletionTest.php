<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserTask;
use App\Models\TaskTemplate;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskCompletionTest extends TestCase
{
    /**
     * Test task completion flow for regular user (angab704@gmail.com)
     *
     * @return void
     */
    public function test_user_can_complete_video_task()
    {
        // Find the actual regular user (not admin)
        $user = User::where('email', 'angab704@gmail.com')->first();

        $this->assertNotNull($user, 'User answar58@proton.me should exist in database');

        // Verify user has wallet
        $this->assertNotNull($user->wallet, 'User should have a wallet');

        // Get or create a pending task for this user
        $task = UserTask::where('user_id', $user->id)
            ->where('status', 'PENDING')
            ->first();

        if (!$task) {
            // Create a test task if none exists
            $taskTemplate = TaskTemplate::where('category', 'VIDEO')
                ->where('is_active', true)
                ->first();

            if (!$taskTemplate) {
                // Create a test task template
                $taskTemplate = TaskTemplate::create([
                    'category' => 'VIDEO',
                    'title' => 'Test Video Task',
                    'description' => 'Watch this test video',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'video_duration_seconds' => 212,
                    'reward_amount' => 299,
                    'completion_time_seconds' => 300,
                    'is_active' => true,
                    'priority' => 5,
                    'max_completions' => 1000,
                    'available_from' => now(),
                    'available_until' => now()->addMonths(1),
                ]);
            }

            $task = UserTask::create([
                'user_id' => $user->id,
                'task_template_id' => $taskTemplate->id,
                'status' => 'PENDING',
                'assigned_at' => now(),
                'expires_at' => now()->addHours(24),
                'reward_amount' => $taskTemplate->reward_amount,
            ]);
        }

        $this->assertNotNull($task, 'User should have at least one task');

        // Get initial balances
        $initialPendingBalance = $user->wallet->pending_balance;
        $initialTotalEarned = $user->wallet->total_earned;
        $initialTransactionCount = Transaction::where('user_id', $user->id)->count();

        // Simulate task completion request
        $response = $this->actingAs($user)->postJson("/tasks/{$task->id}/complete", [
            'response_data' => [
                'video_id' => 'dQw4w9WgXcQ',
                'watched_seconds' => 191,
                'required_seconds' => 190,
                'completion_percentage' => 90
            ],
            'duration' => 200
        ]);

        // Check response
        echo "\nResponse Status: " . $response->status() . "\n";
        echo "Response Content: " . $response->getContent() . "\n";

        if ($response->status() !== 302 && $response->status() !== 200) {
            $this->fail('Task completion failed with status: ' . $response->status() .
                       "\nResponse: " . $response->getContent());
        }

        // Refresh models
        $task->refresh();
        $user->wallet->refresh();

        // Assertions
        $this->assertEquals('COMPLETED', $task->status, 'Task status should be COMPLETED');
        $this->assertNotNull($task->completed_at, 'Task should have completion timestamp');
        $this->assertTrue($task->credited, 'Task should be marked as credited');

        // Check wallet was credited
        $this->assertEquals(
            $initialPendingBalance + $task->reward_amount,
            $user->wallet->pending_balance,
            'Pending balance should increase by reward amount'
        );

        $this->assertEquals(
            $initialTotalEarned + $task->reward_amount,
            $user->wallet->total_earned,
            'Total earned should increase by reward amount'
        );

        // Check transaction was created
        $newTransactionCount = Transaction::where('user_id', $user->id)->count();
        $this->assertEquals(
            $initialTransactionCount + 1,
            $newTransactionCount,
            'A new transaction should be created'
        );

        // Find the transaction
        $transaction = Transaction::where('user_id', $user->id)
            ->where('transaction_type', 'TASK_EARNING')
            ->latest()
            ->first();

        $this->assertNotNull($transaction, 'Transaction should exist');
        $this->assertEquals($task->reward_amount, $transaction->amount);
        $this->assertEquals('PENDING', $transaction->status);
        $this->assertArrayHasKey('matures_at', $transaction->metadata, 'Transaction should have maturation date in metadata');

        // Output success message
        echo "\n";
        echo "✅ TASK COMPLETION TEST PASSED!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "User: {$user->email}\n";
        echo "Task ID: {$task->id}\n";
        echo "Reward: ₦{$task->reward_amount}\n";
        echo "Status: {$task->status}\n";
        echo "Credited: " . ($task->credited ? 'Yes' : 'No') . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Wallet Balances:\n";
        echo "  - Pending: ₦{$user->wallet->pending_balance}\n";
        echo "  - Total Earned: ₦{$user->wallet->total_earned}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Transaction Created:\n";
        echo "  - ID: {$transaction->id}\n";
        echo "  - Amount: ₦{$transaction->amount}\n";
        echo "  - Status: {$transaction->status}\n";
        echo "  - Matures At: {$transaction->matures_at}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }

    /**
     * Test that task completion validates required fields
     */
    public function test_task_completion_requires_response_data()
    {
        $user = User::where('email', 'answar58@proton.me')->first();

        if (!$user) {
            $this->markTestSkipped('Test user not found');
        }

        $task = UserTask::where('user_id', $user->id)
            ->where('status', 'PENDING')
            ->first();

        if (!$task) {
            $this->markTestSkipped('No pending tasks found for user');
        }

        // Try to complete without response_data
        $response = $this->actingAs($user)->postJson("/tasks/{$task->id}/complete", [
            'duration' => 200
        ]);

        $response->assertStatus(422); // Validation error
    }

    /**
     * Test that completed tasks cannot be completed again
     */
    public function test_cannot_complete_already_completed_task()
    {
        $user = User::where('email', 'answar58@proton.me')->first();

        if (!$user) {
            $this->markTestSkipped('Test user not found');
        }

        // Find a completed task
        $completedTask = UserTask::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->first();

        if (!$completedTask) {
            $this->markTestSkipped('No completed tasks found for user');
        }

        // Try to complete again
        $response = $this->actingAs($user)->postJson("/tasks/{$completedTask->id}/complete", [
            'response_data' => ['test' => 'data'],
            'duration' => 200
        ]);

        // Should get error
        $response->assertSessionHasErrors();
    }
}
