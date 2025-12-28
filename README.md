# Notification System - Quick Reference

## How to Send Notifications

### From Any Controller/Class:

```php
use App\Services\NotificationService;
use Illuminate\Mail\Mailables\Attachment;

$notificationService = app(NotificationService::class);

$notificationService->send($user, 'notification_type', [
    'amount' => 10000,
    'any_data' => 'you need',
    'attachments' => [], // Optional PDF attachments
]);
```

### Notification Types Available:
- `withdrawal_requested` (includes PDF receipt)
- `withdrawal_approved`
- `withdrawal_completed`
- `withdrawal_rejected`
- `task_completed`
- `referral_bonus`
- `rank_upgraded`
- `token_rate_change` (auto-sent when admin updates rate)
- **ANY custom type you want!**

### Email Template
All notifications use the **beautiful purple glassmorphism** template at:
- `resources/views/emails/notification.blade.php`

The template includes:
- ✅ Purple gradient header
- ✅ Beautiful message content box
- ✅ "View Dashboard" CTA button
- ✅ Support contact section
- ✅ Professional footer

### Channels (Auto-enabled based on settings):
- ✅ Database (always)
- ✅ Email (if `$settings->email_notifications_enabled`)
- ⏳ SMS (placeholder - returns true)
- ⏳ Firebase (if `$settings->notification_channels['firebase']['enabled']`)
- ⏳ WhatsApp (if `$settings->notification_channels['whatsapp']['enabled']`)
- ⏳ Telegram (if `$settings->notification_channels['telegram']['enabled']`)

### Example - Task Completion with Email:

```php
// Send task completion notification
$notificationService->send($user, 'task_completed', [
    'amount' => 500,
    'task_name' => 'Survey Task',
]);
```

### Example - With PDF Attachment:

```php
use Illuminate\Mail\Mailables\Attachment;

$attachments = [
    Attachment::fromPath(storage_path('app/receipt.pdf'))
        ->as('Receipt.pdf')
        ->withMime('application/pdf')
];

$notificationService->send($user, 'withdrawal_requested', [
    'amount' => $withdrawal->amount_requested,
    'payment_method' => $withdrawal->payment_method,
    'attachments' => $attachments,
]);
```

Done. All channels fire automatically based on global settings.

---

## Withdrawal Limits Configuration

Limits are stored in **global_settings** table, NOT rank table.

### Update via Tinker:

```bash
php artisan tinker

$s = App\Models\GlobalSetting::first();

# Update rank limits
$limits = $s->withdrawal_limits_by_rank;
$limits[1]['per_day'] = 2; // Rank 1 (Bronze)
$limits[2]['per_day'] = 3; // Rank 2 (Silver)
$limits[3]['per_day'] = 5; // Rank 3 (Gold)
$limits[4]['per_day'] = 10; // Rank 4 (Diamond)
$s->withdrawal_limits_by_rank = $limits;
$s->save();
```

**Keys:** `1` = Bronze, `2` = Silver, `3` = Gold, `4` = Diamond (rank IDs)

---

## Token Rate System

### Auto-Tracking (Observer Pattern)
When admin updates `$settings->withdrawal_rate` or `$settings->token_settings`:
1. **GlobalSettingObserver** triggers
2. Creates `TokenRateHistory` record with trend calculation
3. Broadcasts `TokenRateUpdated` event via WebSocket (Pusher)
4. Dispatches batched notifications to all ACTIVE users (500/chunk)

### Files:
- `app/Observers/GlobalSettingObserver.php` - Watches for rate changes
- `app/Events/TokenRateUpdated.php` - WebSocket broadcast event
- `app/Jobs/NotifyUsersOfRateChange.php` - Batched fanout job
- `app/Models/TokenRateHistory.php` - Stores rate snapshots

### Frontend (Dashboard.vue):
```js
Echo.channel('token-rates')
  .listen('.rate.updated', (e) => {
    // e.token_price, e.withdrawal_rate, e.trend, e.trend_percentage
    // Show SweetAlert with rate change
  });
```

### Token Calculation:
```php
tokensRequired = amount / token_price
finalAmount = amount × withdrawal_rate
// Example: ₦10,000 / ₦850 = 11.76 CROW → ₦10,000 × 0.68 = ₦6,800
```

---

## Announcement System

### Display Conditions:
- **Shows:** If active announcements exist for user's status (target_audience)
- **Hides:** If no announcements OR all dismissed by user

### Features:
- Auto-rotate every 7 seconds
- Dismissable (stores in `announcement_users` pivot)
- Non-dismissable (system announcements)
- Targeting: all/active/pending/unverified
- Modal for full details
- Priority ordering

### Models:
- `app/Models/Announcement.php`
- `app/Models/AnnouncementUser.php` (pivot)

### Component:
- `resources/js/Components/AnnouncementCarousel.vue`

### Seeded Data:
Run: `php artisan db:seed --class=AnnouncementSeeder` (6 announcements)

---

## Token Rate Card (Dashboard)

### Display Conditions:
**Shows:** If `$settings->token_settings['fluctuation_enabled'] == true`
**Hides:** If fluctuation disabled

### Data Required (from controller):
```php
'tokenFluctuationEnabled' => $settings->token_settings['fluctuation_enabled'] ?? false,
'tokenRate' => [
    'token_price' => $settings->token_settings['token_price'],
    'withdrawal_rate' => (float) $settings->withdrawal_rate,
    'trend' => $latestHistory->trend ?? 'stable',
    'trend_percentage' => $latestHistory->getTrendPercentage() ?? '0%',
    'is_good_time' => $latestHistory->isGoodWithdrawalTime() ?? false,
]
```

### WebSocket Updates:
- Listens on `token-rates` channel
- Shows SweetAlert on rate change
- Auto-updates card without refresh

---

## AI Task Generation System

### Admin UI:
`/admin/settings` → **AI Task Generation** tab

### Commands:
```bash
# Generate tasks using AI (when inventory low)
php artisan tasks:generate-templates

# Force generation
php artisan tasks:generate-templates --force

# Assign tasks to users daily (cron: 12:01 AM)
php artisan tasks:assign-daily
```

### Services:
- `app/Services/GroqService.php` - AI generation
- `app/Services/AITaskGeneratorService.php` - Task creation

### Features:
- ✅ Auto-generates surveys (10-15 questions, country-specific)
- ✅ Fetches YouTube trending videos + AI verification questions
- ✅ Creates app sync tasks
- ✅ Generates product review tasks
- ✅ Tasks inactive by default (admin review required)
- ✅ Dynamic country handling via `$settings->country_of_operation`
- ✅ Configurable via admin UI (Groq API, YouTube API, thresholds)

### Configuration:
All settings in `global_settings.ai_configuration`:
- `groq_api_key`, `groq_model`, `temperature`
- `youtube_api_key`
- `max_tokens` per task type
- `tasks_to_generate` counts
- `ai_generation_frequency_hours` (default: 168 = weekly)
- `min_task_templates_threshold` (default: 50)
# business-crowdfund
