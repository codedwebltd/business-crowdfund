# FeatureManager Usage Guide

## Why This is Smart 🧠

**The Problem**: Hardcoded features break when admin adds new plan perks.

**The Solution**: JSON-based dynamic features! Admin can add ANY feature without code deployment.

---

## Example Scenarios

### Scenario 1: Admin adds NEW feature to VIP plan
```json
// Admin updates Plan in database:
{
  "max_daily_tasks": 25,
  "task_reward_multiplier": 3.0,
  "priority_support": true,
  "express_withdrawal": true  // ← NEW FEATURE ADDED
}
```

**Code works instantly** (no deployment needed):
```php
if ($user->features()->has('express_withdrawal')) {
    // Process withdrawal in 1 hour instead of 72
}
```

---

### Scenario 2: Admin creates PLATINUM rank
```json
// Admin adds new rank in ranks table:
{
  "name": "PLATINUM",
  "order": 5,
  "benefits": {
    "withdrawal_min": 500,
    "commission_multiplier": 1.15,
    "monthly_bonus": 100000  // ← NEW BENEFIT
  }
}
```

**Code works instantly**:
```php
if ($user->features()->get('monthly_bonus')) {
    $bonus = $user->features()->get('monthly_bonus');
    // Credit monthly bonus automatically
}
```

---

## Usage Examples

### Basic Feature Checking
```php
$user = Auth::user();

// Check if user has a feature
if ($user->features()->has('priority_support')) {
    // Show priority support badge
}

// Get feature value
$maxTasks = $user->features()->get('max_daily_tasks', 8); // Default: 8

// Get multiplier
$multiplier = $user->features()->getTaskRewardMultiplier(); // 1.0, 2.0, or 3.0
```

### Task Management
```php
// Check if user can complete more tasks
if ($user->features()->canCompleteTask()) {
    // Assign new task
} else {
    return "Daily limit reached! You've completed {$user->features()->getMaxDailyTasks()} tasks.";
}

// Show remaining tasks
$remaining = $user->features()->remainingTasks(); // e.g., 3
```

### Withdrawal Validation
```php
$result = $user->features()->canWithdraw(10000);

if ($result['can_withdraw']) {
    // Process withdrawal
} else {
    // Show errors
    foreach ($result['errors'] as $error) {
        echo $error; // "Minimum withdrawal is ₦5,000"
    }
}

// Get limits
$min = $user->features()->getWithdrawalMin(); // ₦5,000 (Bronze) or ₦1,000 (Diamond)
$max = $user->features()->getWithdrawalMax(); // ₦50,000 (Bronze) or ₦500,000 (Diamond)
```

### Commission Calculation
```php
$baseCommission = 300; // 20% of ₦1,500 activation

// Apply rank multiplier automatically
$finalCommission = $user->features()->calculateCommission($baseCommission);
// Bronze: ₦300 × 1.0 = ₦300
// Silver: ₦300 × 1.02 = ₦306
// Gold: ₦300 × 1.05 = ₦315
// Diamond: ₦300 × 1.10 = ₦330
```

### Premium Feature Checks
```php
// Check multiple features
if ($user->features()->hasPrioritySupport()) {
    // Queue in priority support channel
}

if ($user->features()->hasWhatsAppVIP()) {
    // Add to VIP WhatsApp group
}

if ($user->features()->hasInstantWithdrawal()) {
    // Process in 1 hour instead of 72 hours
}
```

### Magic Methods (Dynamic)
```php
// Admin adds "team_bonus_multiplier: 1.5" to Diamond rank

// Code automatically works:
if ($user->features()->hasTeamBonusMultiplier()) {
    $multiplier = $user->features()->getTeamBonusMultiplier(1.0);
    // Applies 1.5x to team earnings
}
```

### Get All Features
```php
$allFeatures = $user->features()->all();
/*
Array(
    "max_daily_tasks" => 15,
    "task_reward_multiplier" => 2.0,
    "priority_support" => true,
    "withdrawal_min" => 3000,
    "commission_multiplier" => 1.02
)
*/
```

---

## Controller Example

```php
class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $features = $user->features();

        return view('tasks.index', [
            'maxTasks' => $features->getMaxDailyTasks(),
            'remaining' => $features->remainingTasks(),
            'rewardMultiplier' => $features->getTaskRewardMultiplier(),
            'hasPrioritySupport' => $features->hasPrioritySupport(),
        ]);
    }

    public function complete(Task $task)
    {
        if (!Auth::user()->features()->canCompleteTask()) {
            return response()->json(['error' => 'Daily task limit reached'], 403);
        }

        // Complete task...
    }
}
```

---

## Blade Template Example

```blade
<div class="dashboard">
    <h2>Your Plan: {{ auth()->user()->features()->planName() }}</h2>
    <h3>Your Rank: {{ auth()->user()->features()->rankName() }}</h3>

    <p>Daily Tasks: {{ auth()->user()->features()->remainingTasks() }} / {{ auth()->user()->features()->getMaxDailyTasks() }}</p>

    @if(auth()->user()->features()->hasPrioritySupport())
        <span class="badge badge-gold">Priority Support ⭐</span>
    @endif

    <p>Withdrawal Limits: ₦{{ number_format(auth()->user()->features()->getWithdrawalMin()) }} - ₦{{ number_format(auth()->user()->features()->getWithdrawalMax()) }}</p>
</div>
```

---

## Benefits

✅ **Future-Proof**: Admin can add unlimited features without code changes
✅ **No Hardcoding**: All limits dynamic from database
✅ **Performance**: Features cached in plan/rank objects
✅ **Clean Code**: `$user->features()->has('x')` instead of messy conditionals
✅ **Type-Safe**: Helper methods provide proper defaults

---

## Admin Can Now Add Features Like:
- `ai_task_priority: true` - Get AI tasks first
- `referral_boost_weekend: 1.5` - 1.5x commissions on weekends
- `early_withdrawal_access: true` - Withdraw before 72hr maturity
- `monthly_cashback: 5000` - Monthly loyalty bonus
- `custom_referral_link: true` - Personalized referral URL
- **Literally ANYTHING** - No code deployment needed!

---

**The magic**: Plans & Ranks use JSON columns, FeatureManager reads dynamically! 🎯
