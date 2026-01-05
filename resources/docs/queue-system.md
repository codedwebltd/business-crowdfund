# Queue System & Job Processing

## Overview

The Qiviotalk Business platform uses **Laravel Queue System** with **database drivers** to handle background jobs asynchronously. This allows time-consuming tasks (like commission distribution, balance maturation, notifications) to run in the background without blocking user requests.

---

## Table of Contents

1. [How It Works](#how-it-works)
2. [Queue Names & Their Purpose](#queue-names--their-purpose)
3. [Queue Workers Configuration](#queue-workers-configuration)
4. [Commission Distribution Flow](#commission-distribution-flow)
5. [Job Types](#job-types)
6. [Monitoring & Management](#monitoring--management)
7. [Troubleshooting](#troubleshooting)
8. [Server Migration Guide](#server-migration-guide)

---

## How It Works

### The Queue Flow

```
┌─────────────────┐
│  User Action    │  (e.g., completes a task)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Application     │  Creates a job and dispatches it to queue
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ jobs Table      │  Job stored in database with queue name
│ (Database)      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Queue Workers   │  Workers listen to queues and process jobs
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Job Executed    │  Commission credited, balance matured, etc.
└─────────────────┘
```

### Key Components

1. **Jobs Table** - Stores pending jobs (queue name, payload, attempts)
2. **Failed Jobs Table** - Stores jobs that failed after max retries
3. **Queue Workers** - PHP processes that continuously poll the queue
4. **Job Classes** - Located in `app/Jobs/` directory

---

## Queue Names & Their Purpose

| Queue Name | Purpose | Priority | Jobs |
|------------|---------|----------|------|
| `default` | General background tasks | High | Default jobs, general processing |
| `validation` | User verification tasks | High | KYC verification, fraud checks |
| `commissions` | Commission disbursement | **Critical** | ProcessCommissionDisbursementJob |
| `snail` | Slow, heavy tasks | Low | ProcessBalanceMaturationJob, batch operations |

### Why Multiple Queues?

- **Priority Management** - Critical tasks (commissions) get processed first
- **Performance** - Slow tasks don't block fast ones
- **Resource Allocation** - Heavy tasks run with dedicated workers
- **Monitoring** - Track different job types separately

---

## Queue Workers Configuration

### Current Setup (Systemd)

We use **systemd services** to manage queue workers with automatic restart on failure.

#### Service File Location
```
/etc/systemd/system/qiviotalk-queue@.service
```

#### Worker Configuration
```ini
[Unit]
Description=Qiviotalk Queue Worker %i
After=network.target

[Service]
Type=simple
User=qiviotalk
Group=qiviotalk
Restart=always
RestartSec=5
WorkingDirectory=/home/qiviotalk/business.qiviotalk.online
ExecStart=/opt/cpanel/ea-php81/root/usr/bin/php /home/qiviotalk/business.qiviotalk.online/artisan queue:work --queue=default,validation,commissions,snail --sleep=3 --tries=2 --max-time=28800
StandardOutput=append:/home/qiviotalk/business.qiviotalk.online/storage/logs/queue-worker-%i.log
StandardError=append:/home/qiviotalk/business.qiviotalk.online/storage/logs/queue-worker-%i.log

[Install]
WantedBy=multi-user.target
```

#### Command Explanation
```bash
php artisan queue:work \
  --queue=default,validation,commissions,snail  # Listen to all queues (priority order)
  --sleep=3                                      # Sleep 3 seconds when no jobs
  --tries=2                                      # Max 2 attempts per job
  --max-time=28800                               # Restart worker after 8 hours (prevents memory leaks)
```

### Managing Workers

#### Start Workers
```bash
systemctl start qiviotalk-queue@{1,2}.service
```

#### Stop Workers
```bash
systemctl stop qiviotalk-queue@{1,2}.service
```

#### Restart Workers (after code changes)
```bash
systemctl restart qiviotalk-queue@{1,2}.service
```

#### Check Worker Status
```bash
systemctl status qiviotalk-queue@1.service
systemctl status qiviotalk-queue@2.service
```

#### View Worker Logs
```bash
tail -f /home/qiviotalk/business.qiviotalk.online/storage/logs/queue-worker-1.log
tail -f /home/qiviotalk/business.qiviotalk.online/storage/logs/queue-worker-2.log
```

#### Check Running Workers
```bash
ps aux | grep "queue:work" | grep qiviotalk
```

---

## Commission Distribution Flow

### When User Completes a Task

```php
// TaskController.php - Line 377
$this->distributeCommissions($user, $task);
```

### What Happens Inside

1. **Commission Calculated** (based on task reward & commission rates)
2. **Recorded in Ledger** with status `PENDING`
   ```php
   CommissionLedger::create([
       'user_id' => $uplineUserId,
       'source_user_id' => $user->id,
       'source_task_id' => $task->id,
       'amount' => $commissionAmount,
       'level' => $level,
       'commission_rate' => $commissionRate,
       'status' => 'PENDING',  // ← Not credited yet!
       'processed_at' => null,
   ]);
   ```

3. **Batch Job Dispatched** (runs every hour via cron)
   ```php
   ProcessCommissionDisbursementJob::dispatch()
       ->onQueue('commissions');  // ← Goes to commissions queue
   ```

4. **Worker Processes Job** - Credits to withdrawable balance
5. **Status Updated** to `PROCESSED`

### Why Batch Processing?

- **Performance** - Don't slow down task completion
- **Atomicity** - All commissions processed together
- **Retry Logic** - Failed disbursements can be retried
- **Scalability** - Handle thousands of commissions efficiently

---

## Job Types

### 1. ProcessCommissionDisbursementJob
**Queue:** `commissions`
**Frequency:** Every hour (cron)
**Purpose:** Credit pending commissions to upline users
**File:** `app/Jobs/ProcessCommissionDisbursementJob.php`

### 2. ProcessBalanceMaturationJob
**Queue:** `snail`
**Frequency:** Every hour (cron)
**Purpose:** Mature pending balance to withdrawable after 72 hours
**File:** `app/Jobs/ProcessBalanceMaturationJob.php`

### 3. AssignTaskBatch
**Queue:** `default`
**Frequency:** Daily (cron)
**Purpose:** Assign daily tasks to active users
**File:** `app/Jobs/AssignTaskBatch.php`

### 4. CheckVpnFraud
**Queue:** `validation`
**Frequency:** On suspicious activity
**Purpose:** Detect VPN/proxy usage for fraud prevention
**File:** `app/Jobs/CheckVpnFraud.php`

### 5. Generate Task Jobs
**Queue:** `default`
**Frequency:** As needed
**Purpose:** Generate tasks using AI (Groq)
**Files:**
- `GenerateVideoTasksJob.php`
- `GenerateSurveyTasksJob.php`
- `GenerateSyncTasksJob.php`
- `GenerateReviewTasksJob.php`

### 6. Notification Jobs
**Queue:** `default`
**Frequency:** Real-time
**Purpose:** Send notifications to users
**Files:**
- `SendRateChangeNotification.php`
- `NotifyUsersOfRateChange.php`

---

## Monitoring & Management

### Command Center (Admin UI)
Navigate to: **Admin → Command Center**

Features:
- ✅ View pending jobs count
- ✅ View failed jobs with details
- ✅ Retry failed jobs individually
- ✅ Clear all failed jobs
- ✅ Monitor job batches
- ✅ View Laravel logs

### Artisan Commands

#### View Queue Status
```bash
php artisan queue:monitor database:default,validation,commissions,snail
```

#### View Failed Jobs
```bash
php artisan queue:failed
```

#### Retry Failed Job
```bash
php artisan queue:retry [job-id]
```

#### Retry All Failed Jobs
```bash
php artisan queue:retry all
```

#### Clear Failed Jobs
```bash
php artisan queue:flush
```

#### Process Jobs Manually (testing)
```bash
php artisan queue:work --queue=commissions --once
```

### Database Queries

#### Check Pending Jobs
```sql
SELECT queue, COUNT(*) as count
FROM jobs
GROUP BY queue;
```

#### Check Failed Jobs
```sql
SELECT COUNT(*) as failed_count
FROM failed_jobs;
```

#### View Pending Commissions
```sql
SELECT COUNT(*) as pending, SUM(amount) as total_amount
FROM commission_ledger
WHERE status = 'PENDING';
```

---

## Troubleshooting

### ❌ Jobs Not Processing

**Symptoms:** Pending jobs stay in queue, commissions not credited

**Solution 1:** Check if workers are running
```bash
ps aux | grep "queue:work" | grep qiviotalk
```

**Solution 2:** Restart workers
```bash
systemctl restart qiviotalk-queue@{1,2}.service
```

**Solution 3:** Check worker logs
```bash
tail -100 /home/qiviotalk/business.qiviotalk.online/storage/logs/queue-worker-1.log
```

---

### ❌ Permission Denied Errors

**Symptoms:** Jobs failing with "Permission denied" in logs

**Common Cause:** Job files owned by `root` instead of `qiviotalk`

**Solution:**
```bash
# Fix ownership
chown -R qiviotalk:qiviotalk /home/qiviotalk/business.qiviotalk.online/app/Jobs

# Fix permissions
chmod -R 644 /home/qiviotalk/business.qiviotalk.online/app/Jobs/*.php
```

---

### ❌ Jobs Stuck in Wrong Queue

**Symptoms:** Jobs dispatched to wrong queue (e.g., commissions go to `default`)

**Cause:** Job class not specifying queue name

**Solution:** Check job class has `onQueue()` method:
```php
ProcessCommissionDisbursementJob::dispatch()
    ->onQueue('commissions');
```

---

### ❌ Workers Not Listening to All Queues

**Symptoms:** Commission/snail jobs not processing, but default jobs work

**Cause:** Workers only configured for `default,validation` queues

**Solution:** Update systemd service to include all queues:
```bash
# Edit service file
nano /etc/systemd/system/qiviotalk-queue@.service

# Ensure ExecStart has all queues:
--queue=default,validation,commissions,snail

# Reload and restart
systemctl daemon-reload
systemctl restart qiviotalk-queue@{1,2}.service
```

---

### ❌ Failed Jobs Piling Up

**Symptoms:** Many failed jobs in `failed_jobs` table

**Solution 1:** Check error messages
```bash
php artisan queue:failed
```

**Solution 2:** Fix underlying issue (database connection, API keys, etc.)

**Solution 3:** Retry failed jobs
```bash
php artisan queue:retry all
```

**Solution 4:** Clear old/invalid failed jobs
```bash
php artisan queue:flush
```

---

## Server Migration Guide

### Pre-Migration Checklist

1. ✅ Stop queue workers on old server
   ```bash
   systemctl stop qiviotalk-queue@{1,2}.service
   ```

2. ✅ Export pending jobs (optional)
   ```bash
   mysqldump -u qiviotalk -p qiviotalk_business jobs > jobs_backup.sql
   mysqldump -u qiviotalk -p qiviotalk_business failed_jobs > failed_jobs_backup.sql
   ```

3. ✅ Document custom queue configurations

---

### On New Server

#### Step 1: Copy Systemd Service File
```bash
scp /etc/systemd/system/qiviotalk-queue@.service user@newserver:/tmp/
```

On new server:
```bash
sudo mv /tmp/qiviotalk-queue@.service /etc/systemd/system/
sudo systemctl daemon-reload
```

#### Step 2: Update Paths (if different)
Edit the service file:
```bash
sudo nano /etc/systemd/system/qiviotalk-queue@.service
```

Update these paths:
- `WorkingDirectory`
- `ExecStart` (PHP path and artisan path)
- `StandardOutput` log path
- `StandardError` log path

#### Step 3: Set Correct Permissions
```bash
# Ensure qiviotalk user owns application
chown -R qiviotalk:qiviotalk /home/qiviotalk/business.qiviotalk.online

# Ensure jobs directory is readable
chmod -R 644 /home/qiviotalk/business.qiviotalk.online/app/Jobs/*.php

# Ensure storage is writable
chmod -R 775 /home/qiviotalk/business.qiviotalk.online/storage
```

#### Step 4: Enable and Start Workers
```bash
# Enable auto-start on boot
sudo systemctl enable qiviotalk-queue@{1,2}.service

# Start workers
sudo systemctl start qiviotalk-queue@{1,2}.service

# Verify running
systemctl status qiviotalk-queue@1.service
systemctl status qiviotalk-queue@2.service
```

#### Step 5: Verify Queue Processing
```bash
# Check workers are running
ps aux | grep "queue:work" | grep qiviotalk

# Check queue status
cd /home/qiviotalk/business.qiviotalk.online
php artisan queue:monitor database:default,validation,commissions,snail

# Process a test job
php artisan queue:work --queue=default --once
```

#### Step 6: Import Pending Jobs (if backed up)
```bash
mysql -u qiviotalk -p qiviotalk_business < jobs_backup.sql
mysql -u qiviotalk -p qiviotalk_business < failed_jobs_backup.sql
```

---

### Post-Migration Verification

1. ✅ Complete a test task and verify commission is recorded
2. ✅ Check commission ledger for pending entries
3. ✅ Wait for hourly cron to run or manually trigger:
   ```bash
   php artisan schedule:run
   ```
4. ✅ Verify commission credited to upline users
5. ✅ Monitor worker logs for errors:
   ```bash
   tail -f storage/logs/queue-worker-1.log
   ```

---

## Best Practices

### ✅ DO

- Keep workers running 24/7 via systemd
- Monitor failed jobs daily via Command Center
- Set `--max-time` to restart workers periodically (prevents memory leaks)
- Use specific queues for different job types
- Log all job activities for debugging
- Restart workers after deploying code changes

### ❌ DON'T

- Don't run long-running tasks (>10 min) synchronously
- Don't dispatch to wrong queues (commission jobs MUST go to `commissions` queue)
- Don't ignore failed jobs - investigate and retry
- Don't kill workers abruptly (use `systemctl stop` for graceful shutdown)
- Don't run workers as `root` user (use `qiviotalk` user)

---

## Quick Reference

### Essential Commands

```bash
# Check worker status
systemctl status qiviotalk-queue@{1,2}.service

# Restart workers (after code deploy)
systemctl restart qiviotalk-queue@{1,2}.service

# View worker logs
tail -f storage/logs/queue-worker-1.log

# Monitor queue
php artisan queue:monitor database:default,validation,commissions,snail

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Check queue worker logs: `storage/logs/queue-worker-*.log`
- Use Command Center: **Admin → Command Center**
- Run diagnostics: `php artisan queue:failed`

---

**Last Updated:** January 2026
**Version:** 1.0
**Maintained By:** Qiviotalk Business Development Team
