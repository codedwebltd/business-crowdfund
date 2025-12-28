# User Registration Flow & Database Setup

## Tables Created on User Registration

When a user registers, these records are created automatically:

### 1. **users** table
```php
User::create([
    'phone_number' => '+2348012345678',
    'full_name' => 'John Doe',
    'password' => Hash::make($password),
    'country' => 'NGA', // Auto-detected from IP
    'referral_code' => 'CP-NGA-847392', // Auto-generated
    'referred_by_id' => $referrer_id, // If referred
    'status' => 'PENDING', // Not activated yet
    'rank_id' => NULL, // Assigned after activation
]);
```

### 2. **wallets** table (Auto-created via Observer/Event)
```php
Wallet::create([
    'user_id' => $user->id,
    'pending_balance' => 0,
    'withdrawable_balance' => 0,
    'total_earned' => 0,
    'total_withdrawn' => 0,
    'currency' => 'NGN',
]);
```

---

## Tables Updated on Plan Activation (After Payment)

### 3. **user_subscriptions** table
```php
UserSubscription::create([
    'user_id' => $user->id,
    'plan_id' => $plan->id,
    'amount_paid' => 1500,
    'payment_method' => 'BANK_TRANSFER',
    'status' => 'PENDING', // Admin verifies → VERIFIED → ACTIVE
]);
```

### 4. **users** table updated
```php
$user->update([
    'status' => 'ACTIVE',
    'plan_id' => $plan->id,
    'activation_amount' => 1500,
    'activation_date' => now(),
]);

$user->assignBronzeRank(); // Sets rank_id to Bronze
```

### 5. **transactions** table (Activation payment logged)
```php
Transaction::create([
    'user_id' => $user->id,
    'transaction_type' => 'PLAN_ACTIVATION',
    'balance_type' => 'WITHDRAWABLE',
    'amount' => 1500,
    'is_credit' => false, // Debit (user paid)
    'status' => 'COMPLETED',
    'description' => 'Premium Plan Activation',
]);
```

---

## Key Relationships

```
User
├── wallet (1:1)
├── rank (N:1)
├── plan (N:1)
├── referrer (N:1 self-referential)
├── directReferrals (1:N self-referential)
├── subscriptions (1:N)
├── transactions (1:N)
├── tasks (1:N)
└── withdrawals (1:N)

Wallet
├── user (1:1)
└── transactions (1:N via user_id)

UserSubscription
├── user (N:1)
├── plan (N:1)
└── verifiedBy (N:1 to User)

Transaction
├── user (N:1)
└── reference (polymorphic: UserTask, Withdrawal, etc.)

UserTask
├── user (N:1)
├── taskTemplate (N:1)
└── transaction (N:1)

Plan
├── users (1:N)
└── subscriptions (1:N)

Rank
├── users (1:N)
└── taskTemplates (1:N via min_rank_id)
```

---

## Registration Flow Summary

1. **User registers** → `users` + `wallets` created, status=PENDING
2. **User pays for plan** → `user_subscriptions` created, status=PENDING
3. **Admin verifies payment** → subscription status=VERIFIED
4. **System activates** → user status=ACTIVE, rank_id=Bronze
5. **User can now complete tasks** → Daily tasks assigned via cron

---

## Important Notes

- **Wallet is auto-created** when user is created (use Observer or Event)
- **Rank is assigned ONLY after activation** (not during registration)
- **Plan is linked after payment** (users table plan_id updated)
- **TaskTemplates are managed by admin** (not auto-created)
- **Transactions log all money movements** (even non-wallet actions like activation fees)

---

## Next Steps

- Create UserObserver to auto-create wallet
- Create ReferralTree table for 40-level deep tracking
- Create Withdrawal model/migration
- Create commission distribution service
