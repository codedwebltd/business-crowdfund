# Models & Migrations Summary

## ✅ Completed Models

### Core User System
1. **User** - Authentication, profile, referrals, KYC
2. **Rank** - Dynamic rank system (Bronze→Diamond+)
3. **Plan** - Subscription tiers (Basic/Premium/VIP)
4. **UserSubscription** - Plan activations & payments

### Financial System
5. **Wallet** - Multi-balance tracking (pending, withdrawable, referral, bonus)
6. **Transaction** - All money movements (earnings, commissions, withdrawals)

### Task System
7. **TaskTemplate** - Task library (admin managed)
8. **UserTask** - Tasks assigned to users

### Referral System
9. **ReferralTree** - 40-level deep network (Materialized Path + Nested Set)

### Withdrawal System
10. **Withdrawal** - Withdrawal requests with priority queue

### Observers
11. **UserObserver** - Auto-create wallet & referral tree on registration

---

## Key Features Implemented

### ✅ No Static ENUMs (Where Possible)
- `transaction_type` → STRING (TASK_EARNING, REFERRAL_BONUS, ADJUSTMENT, etc.)
- `balance_type` → STRING (PENDING, WITHDRAWABLE, REFERRAL, BONUS, etc.)
- `payment_method` → STRING (BANK, USDT, BTC, MONERO, etc.)
- `task_category` → STRING (SURVEY, VIDEO, AI_RATING, TEXT_MODERATION, etc.)
- `status` → STRING in most tables (PENDING, ACTIVE, COMPLETED, FLAGGED, etc.)

### ✅ Dynamic Features
- Plans use JSON `features` column → Admin adds unlimited features
- Ranks use JSON `benefits` column → Admin adds unlimited benefits
- FeatureManager reads dynamically → No code changes needed

### ✅ Location Detection
- LocationTrait copied from public_html
- GeoLite2 database copied to public/location/
- Country auto-detected on registration
- Referral code includes country (CP-NGA-847392)

### ✅ Currency Management (Dynamic)
- **world-countries.json** downloaded to `public/countries/`
- **CountryHelper** class maps country → currency dynamically
- Supports 250+ countries with currency codes, symbols, and flags
- Admin sets `country_of_operation` in global_settings
- All money formatting uses `CountryHelper::formatMoney()`
- NO hardcoded currency symbols anywhere in code

### ✅ Security & Fraud Prevention
- 2FA support (Google Authenticator)
- OTP SMS verification
- Device fingerprinting
- IP tracking
- Multiple fraud detection fields

---

## Still Needed

### High Priority
1. **CommissionService** - Multi-level commission distribution (service class)
2. **Testimonial** - Forced testimonials after first withdrawal (model + migration)

### Medium Priority
3. **RankHistory** - Track rank changes over time
4. **Announcement** - Platform announcements system
5. **DeviceFingerprint** - Multi-account detection
6. **Token** - Token price fluctuation system (from map.md)

### Low Priority (Future)
7. **FeatureUsage** - Track feature limits (if needed)
8. **FailedTask** - Log failed task attempts
9. **NotificationLog** - Track all notifications sent

---

## Migration Order (Important!)

```bash
1. ranks
2. users
3. plans
4. user_subscriptions
5. wallets
6. transactions
7. task_templates
8. user_tasks
9. referral_trees
10. withdrawals
```

Run: `php artisan migrate` (migrations will run in timestamp order automatically)

---

## Relationships Quick Reference

**User has:**
- 1 Wallet (auto-created via UserObserver)
- 1 ReferralTree (auto-created via UserObserver)
- 1 Rank (assigned after activation)
- 1 Plan (via plan_id)
- Many Subscriptions
- Many Transactions
- Many Tasks
- Many Withdrawals
- 1 Referrer (self-referential)
- Many DirectReferrals (self-referential)

**Withdrawal has:**
- 1 User
- 1 ApprovedBy (Admin user)
- Priority score calculation
- Testimonial requirement for first withdrawal

**Everything uses UUIDs** except Rank (auto-increment ID for ordering)

---

## Withdrawal System Features (from map.md)

### ✅ Implemented
- **Priority Queue** - Diamond users processed first
- **Rank-Based Limits** - Min/max amounts from global_settings via FeatureManager
- **Daily Limits** - Withdrawals per day based on rank
- **KYC Enforcement** - Required for amounts above threshold
- **Testimonial Requirement** - Forced testimonial before first withdrawal
- **Multiple Payment Methods** - Bank transfer, USDT, BTC, MONERO (dynamic)
- **Status Tracking** - PENDING → PROCESSING → APPROVED → COMPLETED
- **Admin Controls** - Approve, reject, add notes
- **Auto-refund** - Balance restored on rejection
- **Processing Time Tracking** - Analytics for admin dashboard
