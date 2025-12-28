RESEARCHED BY COWB
CrowdPower Nigeria: Complete System Blueprint


1. USER REGISTRATION & ACCOUNT STRUCTURE
Registration Flow
Step 1: Account Creation

User provides: Phone number, Full name, Email (optional), Bank details
System generates: Unique referral code (e.g., CP-NGR-847392)
SMS verification required (prevents bot accounts)
No immediate payment yet

Step 2: Activation Payment

User selects activation tier:
Example.. (ours will be carefully dynamically implemented)
Basic Plan: ₦5,00 (majority choose this)
Premium Plan: ₦10,000 (claims 2x task earnings)
VIP Plan: ₦25,000 (claims 3x task earnings + priority support)


Payment channels:

Bank Transfer (70% of users) - Manual verification, 2-4 hour delay
Opay/PalmPay (25% of users) - Instant via API
USDT (5% of users) - For international/tech users (Main method)



Step 3: Account Activation

Payment verified by admin or automated webhook
Account status changes: PENDING → ACTIVE
User gains access to task dashboard
Referral link generated and displayed

Database Schema (Users Table) sample
users
├── id (UUID primary key)
├── phone_number (unique, indexed)
├── full_name
├── email (nullable)
├── bank_name
|___role = int 0 = user, 1 = admin -> we will have a permission table where we can assign permission from the admin to users
├── account_number
├── account_name
|___ wallet details such as wallet address, coin_name, coin network -> for withdrawals
├── referral_code (unique, 12 characters)
├── referred_by_id (foreign key to users.id, nullable)
├── plan_type -> we should have a subscription table where admin will set plans and then when user subscribes to a plan, the plan id is stored in their user table, alongside other details of the plan. so take note.
├── activation_amount (decimal)
├── activation_date (timestamp)
├── status (enum: PENDING, ACTIVE, SUSPENDED, BANNED)
├── total_earned (decimal, default 0) -> we need a wallet seprate table to track this 
├── total_withdrawn (decimal, default 0) -> we need a wallet seprate table to track this 
├── withdrawable_balance (decimal, default 0) -> we need a wallet seprate table to track this 
├── pending_balance (decimal, default 0) -> we need a wallet seprate table to track this 
├── rank (enum: BRONZE, SILVER, GOLD, DIAMOND, default BRONZE)
├── direct_referrals_count (integer, default 0)
├── total_team_size (integer, default 0, includes all levels)
├── last_task_completed_at (timestamp, nullable)
├── created_at (timestamp)
├── updated_at (timestamp)

KYC Implementation
Phase 1: Basic KYC (At Registration)
Phone verification (SMS OTP)
Bank account validation (name matching)
BVN NOT required initially (lowers barrier)
wallet details for withdrawal. if there is a way to automatically usea free api to fetch wallet networks depending on selected coins we will do use that in the ui.

Phase 2: Enhanced KYC (Before First Withdrawal)

amongs t these requirements, we will have option in admin to add these fields e.g i can decide to enable nin for this section and utility or only enable selfie and nin etc u get dynamically from admin.
NIN required for withdrawals over ₦50,000
Utility bill for withdrawals over ₦100,000
Selfie verification for Diamond rank users


all these will have admin ui control to set percentage and so on. 
2. TASK SYSTEM ARCHITECTURE
Task Categories & Design
Category A: Survey Tasks (60% of daily tasks)
Structure:
task_templates
├── id
├── category (SURVEY, VIDEO, APP_SYNC, PRODUCT_REVIEW)
├── title (e.g., "Morning Routine Survey")
├── description
├── questions_json (stores question structure)
├── reward_amount (e.g., ₦250)
├── completion_time_seconds (e.g., 120)
├── is_active (boolean)
├── priority (determines task appearance order)
Example Survey Task JSON:
json{
  "task_id": "SURV-001",
  "title": "Social Media Usage Survey",
  "questions": [
    {
      "id": 1,
      "text": "Which social media platform did you use most this morning?",
      "type": "single_choice",
      "options": ["Facebook", "Instagram", "TikTok", "Twitter", "WhatsApp"]
    },
    {
      "id": 2,
      "text": "How many hours do you spend on social media daily?",
      "type": "single_choice",
      "options": ["Less than 1hr", "1-2hrs", "3-5hrs", "More than 5hrs"]
    },
    {
      "id": 3,
      "text": "Would you pay for ad-free social media?",
      "type": "single_choice",
      "options": ["Yes", "No", "Maybe"]
    }
  ],
  "reward": 50,
  "estimated_time": "2 minutes"
}
Category B: Video Watch Tasks (20% of daily tasks)
Structure:
video_tasks
├── id
├── video_url (YouTube embed or hosted video)
├── video_duration_seconds
├── title (e.g., "Watch New Product Launch")
├── verification_questions_json (3-5 questions about video content)
├── reward_amount (e.g., ₦400)
├── minimum_watch_time (80% of video length)
├── is_active
Reality Check: Videos are generic content (product reviews, ads, motivational content). make sure video is tracked well, and other tasks so that if user ont complete we wont let them earn. Platform just embeds public YouTube videos and tracks watch time via frontend JavaScript timers.
Category C: App Usage Data Sync (15% of daily tasks) 
Structure:

for this sync we need to use javascript to sync as much info as we can from the users device. get their device type  maybe combination of pp and js to get detailed info for this sync..
sync_tasks
├── id
├── task_name (e.g., "Daily App Usage Report")
├── description ("Sync your phone app usage data")
├── reward_amount (₦200 - highest single task reward)
├── required_data_points (["top_5_apps", "screen_time_hours", "data_consumed_mb"])
├── sync_duration_seconds (fake progress bar duration: 30-60s)
The trick:

Progress bar shows "Analyzing app usage..." with  technical messages
Frontend collects basic device info (OS, browser type) and any actual data it could get, leaves user's device beyond what any website already sees, get the network if possible just as detailed as we could get
Users BELIEVE they're sharing valuable data because of convincing UI

Example Sync Flow:

User clicks "Sync Data"
Frontend shows animated progress: with detailed data e.g

"Connecting to secure server..." (3s)
"Analyzing app patterns..." (10s)
"Encrypting data..." (5s)
"Uploading to research partners..." (12s)
"Sync complete! ✓"


Backend simply waits for task to complete based on task time, then credits the user..
remeber al these peercentagegs are going to be dynamic the reason am giving certain percent is for u to understand. so every percent comes from tas table.
Category D: Product Review Tasks (5% of daily tasks)
Users rate products/services they've supposedly used. Completely subjective, no verification possible.
Task Assignment Logic
Daily Task Generation (Automated Cron Job - Runs at 12:01 AM)
Algorithm: Dynamic Task Assignment

FOR each active user:
  
  // Reset daily task allowance based on plan
  IF user.plan_type == BASIC:
    max_daily_tasks = 8
  ELSE IF user.plan_type == PREMIUM:
    max_daily_tasks = 15
  ELSE IF user.plan_type == VIP:
    max_daily_tasks = 25
  
  // Clear yesterday's incomplete tasks
  DELETE FROM user_tasks WHERE user_id = user.id AND status = PENDING AND created_at < TODAY
  
  // Assign new tasks for today
  task_pool = SELECT random tasks FROM task_templates WHERE is_active = true
  
  // Mix task types for variety
  surveys = 60% of max_daily_tasks (randomly from survey templates)
  videos = 20% of max_daily_tasks (randomly from video templates)
  syncs = 15% of max_daily_tasks (usually 1-2 sync tasks)
  reviews = 5% of max_daily_tasks (random product review)
  
  // Create user_tasks records
  FOR each task in task_pool:
    INSERT INTO user_tasks (user_id, task_id, status, reward_amount, assigned_at)
  
  // Send push notification
  NOTIFY user: "🎯 {max_daily_tasks} new tasks available! Start earning now."

END FOR
Manual Task Addition (Admin Panel)
Admins can create "Special High Reward Tasks" to drive engagement:

"Limited Time: Nigerian Consumer Preferences Survey - ₦500!"
"Urgent: Tech Product Feedback Needed - ₦300"
Appears as "Featured Task" in dashboard
Limited slots (e.g., "Only 500 users can complete this tass records e.g 1/500 dynamically")

Creates FOMO: Users rush to complete high-reward tasks before slots run out.
Task Completion & Validation
Database Schema:
user_tasks
├── id (UUID)
├── user_id (foreign key)
├── task_id (foreign key to task_templates)
├── status (enum: PENDING, IN_PROGRESS, COMPLETED, EXPIRED)
├── started_at (timestamp, nullable)
├── completed_at (timestamp, nullable)
├── response_data_json (stores user answers)
├── reward_amount (decimal, copied from template)
├── credited (boolean, default false)
├── validation_score (integer, 0-100, for quality checking)
├── assigned_at (timestamp)
├── expires_at (timestamp, 24 hours from assignment)
Completion Flow:
we may track all balance in the wallet  table including task for a user we can create as many columns as necessary fr that.
User Starts Task

Frontend sends: POST /api/tasks/{task_id}/start
Backend updates: user_tasks.status = IN_PROGRESS, started_at = NOW()
Timer starts on frontend


User Submits Answers

Frontend sends: POST /api/tasks/{task_id}/complete with answers JSON
Backend validation checks:

Task not already completed
Task not expired (within 24 hours of assignment)
User actually started the task
Minimum time elapsed (prevents instant clicking through)
All required questions answered
For video tasks: Minimum watch time met (tracked by JS)




Validation Rules

Survey Task Validation:
- Must answer all required questions
- Minimum time: 60 seconds (prevents bots) (dynamic time form task table though)
- Maximum time: 10 minutes (expired after this) (dynamic time form task table though)
- Random pattern detection: Flag users who always select first option

Video Task Validation:  
- Must watch at least 90% of video duration
- Must answer 4/5 verification questions correctly
- Video must fully load (detect if user faked it)

Sync Task Validation:
- Must wait full sync duration (30-60s)
- Can only sync once per 24 hours per task (depending on task timing)
- Must provide basic  to solid device info

Product Review Validation:
- Must write minimum 20 characters
- Cannot submit identical reviews repeatedly
- Rate limiting: Max 3 reviews per day

Crediting Process

IF validation passes:
  
  // Update task record
  UPDATE user_tasks 
  SET status = COMPLETED, 
      completed_at = NOW(),
      credited = true
  WHERE id = task_id
  
  // Credit user account
  UPDATE users 
  SET pending_balance = pending_balance + reward_amount,
      total_earned = total_earned + reward_amount
  WHERE id = user_id
  
  // Log transaction
  INSERT INTO earnings_log (user_id, amount, source, task_id, created_at)
  
  // Real-time notification
  SEND push: "✅ Task completed! ₦{reward_amount} credited to your account"
  
  we are going to user pusher websocket in this project i have websocket details in public_path/.env
ELSE:
  
  // Task failed validation
  UPDATE user_tasks SET status = EXPIRED
  
  // Log failed attempt (track potential cheaters)
  INSERT INTO failed_tasks_log (user_id, task_id, failure_reason)
  
  // Notify user
  SEND push: "❌ Task validation failed. Please follow instructions carefully."

END IF

Anti-Fraud Measures

fraud_detection_rules
├── Velocity Check: Max 15 tasks per hour (prevents bot-like behavior)
├── Pattern Detection: Flag users with identical response patterns
├── Device Fingerprinting: Detect multiple accounts from same device
├── IP Monitoring: Flag users using VPNs or data center IPs
├── Completion Speed: Flag surveys completed in under timing  diff from actual task time 
├── Referral Abuse: Flag users who refer accounts that never complete tasks
Action on Fraud Detection:

First offense: Warning notification
Second offense: 48-hour task suspension
Third offense: Account review, possible ban
Persistent fraud: Permanent ban + balance forfeiture

Reality: These rules exist primarily to slow down sophisticated users who might game the system, because tasks represent real value. The platform wants engagement because it is legitimate but can't allow exploitation.

3. EARNINGS STRUCTURE & BALANCE MANAGEMENT
Balance Types (Critical Design Decision)
Users have many columns in wallet table for storng diff balance separate balances:

Pending Balance (Non-withdrawable)

Newly completed task rewards go here
Must "mature" for 48-72 hours before becoming withdrawable
Creates psychological buffer
Reduces immediate withdrawal pressure


Withdrawable Balance (Can request withdrawal)

Matured pending balance transfers here automatically
Referral bonuses go directly here (incentivizes recruiting)
Subject to minimum withdrawal threshold



Database Implementation:
balance_transactions
├── id (UUID)
├── user_id (foreign key)
├── transaction_type (enum: TASK_EARNING, REFERRAL_BONUS, RANK_BONUS, MATURATION, WITHDRAWAL_REQUEST, WITHDRAWAL_COMPLETED)
├── amount (decimal)
├── balance_type (enum: PENDING, WITHDRAWABLE)
├── source_description (text, e.g., "Survey task #1234 completed")
├── status (enum: PENDING, APPROVED, REJECTED, COMPLETED)
├── reference_id (nullable, links to related record)
├── created_at (timestamp)
├── processed_at (timestamp, nullable)
Task Earnings Flow
TIMELINE: Task Completion to Withdrawal

Day 1, 10:00 AM:
→ User completes "Morning Survey" task
→ ₦50 credited to PENDING balance
→ Status: Locked, matures in 72 hours

Day 1, 2:00 PM:
→ User completes "Video Watch" task  
→ ₦100 credited to PENDING balance
→ Status: Locked, matures in 72 hours

Day 4, 10:00 AM (72 hours later):
→ Automated cron job runs
→ ₦50 from Day 1 morning task transfers to WITHDRAWABLE balance
→ User receives notification: "₦50 is now available for withdrawal!"

Day 4, 2:00 PM:
→ ₦100 from Day 1 afternoon task transfers to WITHDRAWABLE balance
→ Withdrawable balance: ₦150
→ Still below ₦5,000 minimum, cannot withdraw yet
Maturation Cron Job (Runs hourly):
SELECT * FROM balance_transactions 
WHERE balance_type = PENDING
  AND transaction_type = TASK_EARNING
  AND created_at <= NOW() - INTERVAL 72 HOURS (this hours will be set from global settings in admin so it can change anytime admin wants.)
  AND status = PENDING

FOR each transaction:
  
  // Transfer to withdrawable balance
  UPDATE users 
  SET pending_balance = pending_balance - transaction.amount,
      withdrawable_balance = withdrawable_balance + transaction.amount
  WHERE id = transaction.user_id
  
  // Update transaction status
  UPDATE balance_transactions 
  SET status = COMPLETED, processed_at = NOW()
  WHERE id = transaction.id
  
  // Notify user
  SEND notification

END FOR

4. REFERRAL SYSTEM (THE REAL MONEYMAKER)
Family Tree Structure (40-Level Deep Recursive Network (the level deep integer will be set by the admin in global settings ))
Database Schema:
referral_tree
├── id (UUID)
├── user_id (foreign key, the person in this node)
├── parent_id (foreign key to users.id, their direct referrer)
├── level (integer, 1 = direct, 2 = indirect, etc., max e.g 40)
├── root_ancestor_id (foreign key, the top-level referrer in this chain)
├── path (ltree or JSON array, full ancestry path)
├── left_boundary (integer, for nested set model)
├── right_boundary (integer, for nested set model)
├── created_at (timestamp)
Implementation Model: Materialized Path + Nested Set Hybrid
Why This Design:

Materialized Path: Fast ancestry queries ("who are all my downlines?")
Nested Set: Fast subtree operations ("count team size at all levels")
Level field: Direct commission calculation

Example Tree Structure:
User A (Level 0 - Root)
│
├─ User B (Level 1, referred by A)
│  ├─ User D (Level 2, referred by B)
│  │  └─ User G (Level 3, referred by D)
│  └─ User E (Level 2, referred by B)
│
└─ User C (Level 1, referred by A)
   └─ User F (Level 2, referred by C)
      ├─ User H (Level 3, referred by F)
      └─ User I (Level 3, referred by F)
         └─ User J (Level 4, referred by I)
            └─ ... (continues to Level 40)
Path Representation:
User A: path = "A"
User B: path = "A.B"
User D: path = "A.B.D"
User G: path = "A.B.D.G"
User J: path = "A.B.E.F.I.J"
Query Examples:
sql-- Get all direct referrals of User A
SELECT * FROM users WHERE referred_by_id = 'A';

-- Get entire team under User A (all 40 levels)
SELECT * FROM referral_tree WHERE path LIKE 'A.%';

-- Count team size at each level for User A
SELECT level, COUNT(*) 
FROM referral_tree 
WHERE root_ancestor_id = 'A' 
GROUP BY level 
ORDER BY level;

-- Get all ancestors of User J (for commission distribution)
SELECT * FROM referral_tree 
WHERE 'A.B.E.F.I.J' LIKE CONCAT(path, '.%') 
ORDER BY level DESC;
Commission Structure (40-Level Deep)
Global Configuration:
commission_rates (stored in database, adjustable)
├── Level 1 (Direct): 20% of activation fee + 10% of task earnings
├── Level 2: 10% of activation fee + 5% of task earnings
├── Level 3: 5% of activation fee + 3% of task earnings
├── Level 4: 3% of activation fee + 2% of task earnings
├── Level 5: 2% of activation fee + 1% of task earnings
├── Level 6-10: 1% of activation fee + 0.5% of task earnings
├── Level 11-20: 0.5% of activation fee + 0.25% of task earnings
├── Level 21-30: 0.25% of activation fee only
├── Level 31-40: 0.1% of activation fee only (negligible but exists)
all dynamic percentage based n actual task and actual value set.
Why 40 Levels?

Psychological: "Unlimited earning potential"
Mathematical: Creates passive income from distant downlines
Practical: Beyond Level 5, commissions become tiny but still motivate
Reality: Most users never recruit beyond Level 3

Referral Registration Flow
Step 1: New User Clicks Referral Link
URL: https://crowdpower.ng/register?ref=CP-NGR-847392
referal link should be built with country abbreviation coz we will have users in diff countries so we can get the abbr from users tabe country_coulumn

Frontend captures referral code from URL
Stores in session/localStorage
Step 2: New User Completes Registration
Backend process:

1. Validate referral code exists
   SELECT id FROM users WHERE referral_code = 'CP-NGR-847392'
   
2. If valid, link new user to referrer
   INSERT INTO users (referred_by_id, ...) VALUES (referrer.id, ...)
   
3. Build referral tree path
   referrer_path = SELECT path FROM referral_tree WHERE user_id = referrer.id
   new_user_path = referrer_path + "." + new_user.id
   
4. Insert into referral tree
   INSERT INTO referral_tree (user_id, parent_id, level, path, root_ancestor_id)
   
5. Increment referrer's counters
   UPDATE users 
   SET direct_referrals_count = direct_referrals_count + 1
   WHERE id = referrer.id
Step 3: New User Activates Account (Pays ₦1,500)
Commission Distribution Cascade:

When new_user pays ₦1,500:

1. Query all ancestors from referral tree
   SELECT user_id, level, path 
   FROM referral_tree 
   WHERE new_user.path LIKE CONCAT(path, '.%')
   ORDER BY level ASC
   LIMIT 40 ($settings->referral_level_limit)
   
2. For each ancestor, calculate commission
   
   Level 1 (Direct referrer):
   ├─ Commission: ₦1,500 × 20% (dynamic percent) = ₦300
   ├─ Credit to: referrer.withdrawable_balance (instant!)
   ├─ Log: INSERT INTO earnings_log
   ├─ Notify: "🎉 You earned ₦300 from referral bonus!"
   
   Level 2 (Referrer's referrer):
   ├─ Commission: ₦1,500 × 10% = ₦150
   ├─ Credit to withdrawable balance
   ├─ Log and notify
   
   Level 3:
   ├─ Commission: ₦1,500 × 5% = ₦75
   
   ... (continues through all levels up to 40)
   
   Level 10:
   ├─ Commission: ₦1,500 × 1% = ₦15
   
   Level 20:
   ├─ Commission: ₦1,500 × 0.5% = ₦7.50
   
   Level 40:
   ├─ Commission: ₦1,500 × 0.1% = ₦1.50

3. Platform keeps remainder
   Total paid in commissions: ~₦650 (43%)
   Platform keeps: ₦850 (57%)
Daily Task Earnings Commission
Trigger: When downstream user completes tasks
User Z (Level 3 downline) completes daily tasks:
- Morning Survey: ₦50
- Video Watch: ₦100  
- Data Sync: ₦200
Total earned today: ₦350

Commission Distribution:

Level 1 (User Y, direct referrer of Z):
├─ Commission: ₦350 × 10% = ₦35
├─ Credit immediately to withdrawable balance

Level 2 (User X, referred User Y):
├─ Commission: ₦350 × 5% = ₦17.50
├─ Credit immediately

Level 3 (User W, referred User X):
├─ Commission: ₦350 × 3% = ₦10.50
├─ Credit immediately

Levels 4-5: Smaller percentages continue
Levels 6-40: Even smaller, but still tracked
Aggregation Cron Job (Runs at end of day):
Instead of real-time micro-transactions (expensive): rather track ad pay latter (commission_ledger table)

1. Accumulate commissions in temporary table throughout day
2. At midnight, batch process: using laravel job dispatcer in snail form to avoid server overloading in batches too coz we are expecting huge memebers
   
   FOR each user with pending commissions:
     
     total_commission = SUM(all commissions earned today from downline)
     
     UPDATE users 
     SET withdrawable_balance = withdrawable_balance + total_commission,
         total_earned = total_earned + total_commission
     
     INSERT INTO earnings_log (user_id, amount, source: "Daily Team Earnings")
     
     SEND notification: "💰 You earned ₦{total_commission} from your team today!"
     
   END FOR

5. RANK SYSTEM & PROGRESSION
Rank Calculation (Automated, Runs Daily)
Rank Criteria:
BRONZE (Default):
├─ Direct referrals: 0-9
├─ Total team size: 0-50
├─ Commission rates: Base level
├─ Benefits: Weekly withdrawals only

SILVER:
├─ Direct referrals: 10-29
├─ Total team size: 50-200
├─ Activation requirement: All direct referrals must be active (completed at least 1 task in last 7 days)
├─ Commission boost: +2% on all levels
├─ Benefits: Daily withdrawals, WhatsApp VIP group access

GOLD:
├─ Direct referrals: 30-99
├─ Total team size: 200-1000
├─ Monthly team volume: ₦100,000+ in total activations
├─ Commission boost: +5% on all levels
├─ Benefits: Priority withdrawal processing (24hr max), "Field Manager" title, profile badge

DIAMOND:
├─ Direct referrals: 100+
├─ Total team size: 1000+
├─ Monthly team volume: ₦500,000+ in total activations
├─ Stability requirement: Rank maintained for 3 consecutive months
├─ Commission boost: +10% on all levels
├─ Benefits: Instant withdrawals, monthly leadership bonus ₦50k, annual summit invitation, featured on website
Rank Evaluation Cron (Runs daily at 2 AM):
FOR each user:

all these features can be tracked ina  seprate table which ill cascade and relaated to the plans 
features_manager it will have human readable field e.g Team Size 50 and programtic tracking which is team_size = 50 all json i did this kind of thing in public_path/database/migrations/subscriptions & featuresmanager u cn see.
  
  // Count active metrics
  direct_referrals = COUNT direct referrals
  total_team = COUNT all downlines across 40 levels
  active_directs = COUNT direct referrals WHERE last_task_completed_at > NOW() - 7 days
  monthly_volume = SUM(activation_amount) from all downlines in last 30 days
  
  // Determine eligible rank
  IF direct_referrals >= 100 AND total_team >= 1000 AND monthly_volume >= 500000:
    eligible_rank = DIAMOND
  ELSE IF direct_referrals >= 30 AND total_team >= 200 AND monthly_volume >= 100000:
    eligible_rank = GOLD
  ELSE IF direct_referrals >= 10 AND total_team >= 50 AND active_directs == direct_referrals:
    eligible_rank = SILVER
  ELSE:
    eligible_rank = BRONZE
  END IF
  
  // Check stability for Diamond (3 months at Gold minimum)
  IF eligible_rank == DIAMOND:
    IF user has been GOLD for < 3 months:
      eligible_rank = GOLD
    END IF
  END IF
  
  // Update if rank changed
  IF user.rank != eligible_rank:
    
    old_rank = user.rank
    UPDATE users SET rank = eligible_rank WHERE id = user.id
    
    // Log rank change
    INSERT INTO rank_history (user_id, old_rank, new_rank, changed_at)
    
    // Notify user
    IF eligible_rank > old_rank:
      SEND notification: "🎊 Congratulations! You've been promoted to {eligible_rank} rank!"
      SEND email with rank benefits details
    ELSE:
      SEND notification: "⚠️ Your rank has been adjusted to {eligible_rank}. Keep building your team!"
    END IF
    
  END IF

END FOR

we need a testimonial system, after first withdrawals, users are forced to testify otherwise they wont be able to make take survey or even withdraw. that means u are forced to testify of our good deeds and testimonials should not be less than 50 words and the table should have enough data and relationship to the user so that we can be able to track the user who testified and show their data accodingly also admin will manually approe testimonies


Rank-Based Commission Multipliers
Applied to all commission calculations:
Example: Level 1 direct referral activates (₦1,500 payment)

Base commission (Bronze): ₦1,500 × 20% = ₦300

IF user.rank == SILVER:
  commission = ₦300 × 1.02 = ₦306
  
IF user.rank == GOLD:
  commission = ₦300 × 1.05 = ₦315
  
IF user.rank == DIAMOND:
  commission = ₦300 × 1.10 = ₦330

Same multiplier applies to ALL 40 levels of commissions
Monthly Leadership Bonus (Diamond Rank Only):
Eligibility check (runs on 1st of month):

IF user.rank == DIAMOND AND rank_maintained_for >= 3 months:
  
  // Calculate bonus based on team performance
  base_bonus = ₦50,000
  
  // Performance multiplier all these base and digits will be set in global settings admin can easily manipulate.
  IF monthly_team_volume > ₦1,000,000:
    bonus = base_bonus × 1.5 = ₦75,000
  ELSE IF monthly_team_volume > ₦750,000:
    bonus = base_bonus × 1.25 = ₦62,500
  ELSE:
    bonus = base_bonus
  END IF
  
  // Credit bonus
  UPDATE users SET withdrawable_balance = withdrawable_balance + bonus
  
  INSERT INTO earnings_log (user_id, amount, source: "Diamond Leadership Bonus")
  
  SEND notification: "💎 Your Diamond Leadership Bonus of ₦{bonus} has been credited!"
i need a solid table that tracks earnings in the entire system... so that admin will know how much platform has made how much to set aside for paying users and how much to keep in wallet. this will help eliminate mistakes.
END IF

6. WITHDRAWAL SYSTEM (CRITICAL CONTROL MECHANISM) strictly controlled in admin thats where we will set all trenshold and amounts..
e.g: $settings->minimum_withdrawal etc..

Withdrawal Rules & Thresholds
Minimum Withdrawal Amounts:
Bronze: ₦5,000 minimum
Silver: ₦3,000 minimum (reduced as benefit)
Gold: ₦2,000 minimum
Diamond: ₦1,000 minimum

Maximum per withdrawal:
Bronze/Silver: ₦50,000
Gold: ₦100,000  
Diamond: ₦500,000

Daily withdrawal limits:
Bronze: 1 withdrawal per week
Silver: 1 withdrawal per day
Gold: 2 withdrawals per day
Diamond: Unlimited
Why These Limits?

Prevents bank runs (too many simultaneous large withdrawals)
Forces users to accumulate larger balances (psychological investment)
Tier benefits incentivize recruiting to reach higher ranks
Diamond users (biggest recruiters) get preferential treatment to keep them happy

Withdrawal Request Flow
Database Schema:
withdrawal_requests
├── id (UUID)
├── user_id (foreign key)
├── amount_requested (decimal)
├── bank_name
├── account_number
├── account_name
|--- wallet details (main method)
├── status (enum: PENDING, PROCESSING, APPROVED, COMPLETED, REJECTED)
├── requested_at (timestamp)
├── approved_at (timestamp, nullable)
├── processed_at (timestamp, nullable)
├── admin_notes (text, nullable)
├── transaction_reference (string, nullable)
├── rejection_reason (text, nullable)
├── priority_score (integer, calculated based on user rank and history)
User Initiates Withdrawal:
Frontend: User clicks "Withdraw ₦10,000"

Validation checks:
1. Amount >= minimum for user's rank
2. Amount <= maximum for user's rank  
3. Amount <= user.withdrawable_balance
4. User hasn't exceeded daily withdrawal limit
5. User's account is ACTIVE (not suspended)
6. User has completed KYC if amount > threshold

IF all checks pass:
  
  // Deduct from withdrawable balance immediately
  UPDATE users 
  SET withdrawable_balance = withdrawable_balance - amount
  WHERE id = user_id
  
  // Create withdrawal request
  INSERT INTO withdrawal_requests (user_id, amount_requested, status: PENDING, ...)
  
  // Calculate priority score
  priority_score = calculate_priority(user)
  
  // Add to processing queue
  NOTIFY admin dashboard: "New withdrawal request: ₦{amount}"
  
  // Notify user
  SEND notification: "✅ Withdrawal request received. Processing within 24-48 hours."

ELSE:
  
  SHOW error message with specific reason
  
END IF
Priority Scoring Algorithm:
Function calculate_priority(user):
  
  score = 0
  
  // Rank-based priority (Diamond users processed first)
  IF user.rank == DIAMOND: score += 100
  ELSE IF user.rank == GOLD: score += 50
  ELSE IF user.rank == SILVER: score += 25
  ELSE: score += 0
  
  // Team size bonus (big recruiters get priority)
  score += (user.total_team_size / 10)
  
  // Account age bonus (older accounts slightly prioritized)
  days_active = DAYS_BETWEEN(user.activation_date, NOW())
  score += (days_active / 7)
  
  // Previous withdrawal success (reliable users prioritized)
  successful_withdrawals = COUNT withdrawals WHERE status = COMPLETED
  score += (successful_withdrawals × 5)
  
  // Penalty for high withdrawal frequency
  recent_withdrawals = COUNT withdrawals in last 7 days
  IF recent_withdrawals > 3: score -= 20
  
  RETURN score

End Function
Admin Processing Dashboard
Processing Queue View:
Withdrawal requests ordered by:
1. Priority score (DESC)
2. Requested timestamp (ASC - FIFO within same priority)

Admin sees:
├── Request ID
├── User name + rank badge
├── Amount requested
├── Priority score
├── Days since request
├── User's total team size
├── User's total withdrawn (lifetime)
├── Actions: [Approve] [Reject] [Flag for Review]
Manual Approval Process:
Admin clicks [Approve]:

1. Verify bank details match KYC data
2. Check user hasn't been flagged for fraud
3. Confirm platform has sufficient liquidity
4. Update status: PENDING → APPROVED

Automated transfer initiation:
├── IF amount < ₦10,000: Use automated bank transfer API
├── IF amount >= ₦10,000: Queue for manual bank transfer (reduce platform fees)

Generate transaction reference
Update withdrawal_requests: status = PROCESSING, approved_at = NOW()

Notify user: "✅ Your withdrawal has been approved! Funds will arrive within 24 hours."
Automated Bank Transfer (API Integration):
For platforms using Paystack/Flutterwave Transfer API:

API call:
POST /transfer
{
  "amount": amount_requested × 100 (convert to kobo),
  "recipient": recipient_code (from previous recipient creation),
  "reference": withdrawal.transaction_reference,
  "reason": "CrowdPower withdrawal"
}

Response handling:
IF API returns success:
  UPDATE withdrawal_requests SET status = COMPLETED, processed_at = NOW()
  SEND notification: "💰 ₦{amount} has been sent to your account!"
  
ELSE IF API returns insufficient balance:
  // Platform payout will arrive soon exected delay due to network delays!
  UPDATE withdrawal_requests SET status = PENDING
  SEND admin alert: "⚠️ Insufficient platform balance for withdrawals!"
  SEND user notification: "Processing delayed due to high volume. Funds will arrive within 48-72 hours."
  
ELSE IF API returns other error:
  UPDATE withdrawal_requests SET status = REJECTED, rejection_reason = error
  // Refund user's balance
  UPDATE users SET withdrawable_balance = withdrawable_balance + amount
  SEND notification: "❌ Withdrawal failed. Funds have been returned to your account. Please contact support."

END IF
Liquidity Management (The Ponzi Sustainability Mechanism)
Daily Liquidity Report (Generated at midnight):
Calculate key metrics: (!important to store ind db too)

1. Total platform balance across all bank accounts/wallets
2. Pending withdrawal requests (total amount)
3. Expected withdrawals tomorrow (based on historical data)
4. New deposits today (activation payments received)
5. Burn rate: Withdrawals / New deposits ratio

Liquidity Health Status:
├── HEALTHY: Burn rate < 0.7 (more money coming in than going out)
├── CAUTION: Burn rate 0.7-0.9 (withdrawals approaching deposits)
├── CRITICAL: Burn rate > 0.9 (withdrawals exceeding deposits)
├── COLLAPSE IMMINENT: Burn rate > 1.2 for 3 consecutive days

Alert triggers:
IF status == CAUTION:
  NOTIFY admins: "⚠️ Withdrawal pressure increasing. Consider slowing approval rate."
  
IF status == CRITICAL:
  NOTIFY admins: "🚨 URGENT: Liquidity crisis approaching. Implement delay tactics."
  Automatically increase withdrawal processing time: 24hrs → 72hrs
  
IF status == COLLAPSE IMMINENT:
  NOTIFY admins: "☠️ CRITICAL: Platform cannot sustain current withdrawal rate."
  Execute controlled collapse plan (detailed later)
Delay Tactics (When Liquidity is Low):
Tactic 1: Extend processing times
- Change user-facing message: "Due to unprecedented growth, processing now takes 3-5 business days"
- Prioritize only Diamond users to keep top recruiters happy
- Bronze/Silver withdrawals delayed 7-14 days
(important till we replenish our platform pocket to payout again these are important measure)

Tactic 2: Introduce additional KYC requirements
- "CBN now requires NIN verification for all withdrawals"
- "Submit utility bill for amounts over ₦20,000"
- Creates friction, many users abandon withdrawal attempts giving us time to replenish and serve them instead of panicking.

Tactic 3: Withdrawal fees
- Announce temporary "processing fee" of 5-10%
- Discourages withdrawals, users wait for "fee-free period"
we should also have a table that handles announcement with proper columns such as status etc.. in settings.

Tactic 4: Platform "upgrades"
- "System maintenance for improved security - withdrawals paused 72 hours"
- Buys time to collect more deposits
- If successful: Resume withdrawals to rebuild trust
- If unsuccessful: Extend maintenance indefinitely, eventually.....

Tactic 5: Token migration (advanced schemes)
- "Migrate to new token system for better rewards"
- Force users to convert current balance at reduced rate
- Example: 10,000 old coins = 5,000 new coins (50% haircut)
- Users who complain: "This is for platform sustainability" we are dong our best to amek evryone happy.
we should also have a token system where i can set token value from $settings->token_price it is just like how bitcoin fluctuates up and down that means if token is down users will sitback and wait for better days before making withdrawals.(important)

7. NOTIFICATION & ENGAGEMENT SYSTEM
Push Notification Strategy
Purpose: Keep users engaged, reduce withdrawal requests, encourage recruiting
Automated Triggers:
Daily task reminders:
├── 8:00 AM: "☀️ Good morning! 8 new tasks available. Start earning now!" counts with actual platform actual active tasks.
├── 2:00 PM: "⏰ Don't miss out! 5 tasks still incomplete today."
├── 9:00 PM: "🌙 Last chance! Complete tasks before they expire at midnight."

Earnings notifications:
├── Real-time: "✅ Task completed! ₦50 credited."
├── Real-time: "🎉 Referral bonus! ₦300 earned from {friend_name} joining."
├── Daily summary: "💰 Today's earnings: ₦850. Keep it up!"

Maturation reminders:
├── "⏳ ₦500 will be available for withdrawal in 24 hours!"
├── "✅ ₦500 is now withdrawable!"

Team activity:
├── "👥 {name} joined your team! You earned ₦300."
├── "🔥 Your team earned ₦2,450 today!"
├── "⭐ {name} just reached Silver rank in your downline!"

Withdrawal updates:
├── "✅ Withdrawal request received."
├── "🏦 Withdrawal approved! Funds arriving soon."
├── "💰 ₦10,000 has been sent to your account!"

Rank progression:
├── "📊 You're 5 referrals away from Silver rank!"
├── "🎊 Congratulations! You've been promoted to Gold rank!"

Scarcity/FOMO triggers:
├── "🔥 Special task: Only 100 slots left! Earn ₦500!"
├── "⚠️ Limited time: Double referral bonus this weekend!"
├── "🎯 Refer 3 people this week, win ₦5,000 bonus!"
In-App Messaging
Announcement Banner (Top of dashboard): and firebase ready when we do the mobile app for ios and android
Messages rotation:
├── "🎉 CrowdPower paid out ₦45M to members this month!"
├── "📈 Join 250,000+ Nigerians earning daily!" the country should be dynamic based on country set in the admin $settings->country_of_operation
├── "🏆 Top earner this week made ₦180,000!"
├── "⚠️ CBN registration in progress - withdrawals may take 3-5 days" (when liquidity is low)

Email Campaign (For Inactive Users)
Day 3 of inactivity:
Subject: "You have ₦350 waiting! Complete tasks to withdraw."
Body: Reminder of pending balance, task opportunities
all notification ready for database,firebase,email we will have /services/Notifications/email,firebase,etc..
we will have whatsapp notificationservice,telegram ntification service as well

Day 7 of inactivity:
Subject: "Your account is at risk of deactivation"
Body: "Complete at least 1 task in the next 48 hours to keep account active"
Purpose: Create urgency, prevent dormant accounts

Day 14 of inactivity:
Subject: "Your friend {name} just earned ₦12,000 this week!"
Body: Social proof from their network, testimonial video
Purpose: Re-engage through peer influence
WhatsApp Group Strategy (Silver rank and above)
VIP Group Benefits:
Daily updates:
├── 9:00 AM: "Good morning team! Today's featured task: {task_name}"
├── Throughout day: "🎉 Congratulations {name} on reaching {milestone}!"
├── 8:00 PM: "Today's top earners: 1. {name} - ₦15,000..." 
will be sent automatically from our cronjob to whatsapp groups we will have telegram community big community so it should also be reyd to spread that measn there will be a settings in admin for controlling  social channels for notifications such as ther api and necessary data for sending can be json column auto generated dynamic  form in admin ui 

Strategy sharing:
├── Admins post: "How to build your team faster" tips
├── Success stories: Members share screenshots of withdrawals
├── Motivation: "Your breakthrough is one referral away!"
we will build a sophosicated notification system that broadcast to all available chnnels asap from the admin ui.. it wil ring on every notification channel we specify..

Exclusive offers:
├── "VIP members only: Extra ₦200 on all tasks today!"
├── "Silver+ exclusive task unlocked!"

Admin presence:
├── "CEO" occasionally posts motivational messages
├── Responds to concerns publicly (builds trust)
├── Announces new features, platform updates
Psychological Purpose:

Creates community feeling (not just a platform)
Social proof through constant success stories
FOMO for Bronze users (motivates upgrading rank)
Instant support reduces withdrawal requests from anxiety


8. FRAUD DETECTION & ACCOUNT SECURITY
Multi-Account Detection
Database Schema:
device_fingerprints
├── id (UUID)
├── user_id (foreign key)
├── fingerprint_hash (unique device identifier)
├── ip_address
├── user_agent
├── screen_resolution
├── timezone
├── browser_plugins_hash
├── first_seen (timestamp)
├── last_seen (timestamp)

login_history
├── id (UUID)
├── user_id (foreign key)
├── ip_address
├── device_fingerprint_id (foreign key)
├── login_timestamp
├── location (city, country from IP geolocation)
Detection Logic:
On user registration or login:

1. Generate device fingerprint from browser data
2. Check for suspicious patterns:

Pattern 1: Multiple accounts, same device
SELECT user_id, COUNT(*) 
FROM device_fingerprints 
WHERE fingerprint_hash = current_fingerprint 
GROUP BY user_id

IF count > 1:
  FLAG: "Multiple accounts detected on same device"
  ACTION: Require additional KYC verification for all accounts
  penalize

Pattern 2: Multiple accounts, same IP (within 24 hours)
SELECT COUNT(DISTINCT user_id) 
FROM login_history 
WHERE ip_address = current_ip 
  AND login_timestamp > NOW() - INTERVAL 24 HOURS

IF count > 3:
  FLAG: "Suspicious registration activity from IP"
  ACTION: Temporarily block new registrations from this IP

Pattern 3: Referral chain abuse
// Detect if User A refers User B, User B refers User C, User C refers User A (circular)
// Or if all referrals come from same IP/device, this should also be cecked durring account creation especialy when the user is refered we check their refrral ansestory line and validate if it is from same device and so on.

QUERY referral tree for circular patterns
IF circular reference detected:
  FLAG: "Referral scheme abuse"
  ACTION: Freeze all accounts in chain, require manual review and notify them that a referral abuse was detected from the $abuser_user->name so the accounts in that chain  is under investigations 

Pattern 4: Bot-like task completion
// User completing tasks too quickly or with identical patterns. penalize

IF task completion speed < 30 seconds per task:
  FLAG: "Bot-like behavior"
  they cant complete task again for that day. and should be notified of teir abuse and penality reasons.
  
IF user always selects first answer option:
  FLAG: "Pattern manipulation"
  increase completion time and notify them /warn them with reasons.
  
ACTION: Suspend task access for 48 hours, require CAPTCHA verification
Account Suspension Triggers
Automatic suspension (no manual review needed):

1. Failed login attempts > 5 in 20min
   → Suspend for 24 hours

2. Withdrawal to different bank details than registered
   → Suspend withdrawals, require KYC verification

3. Referral fraud detected (mass fake accounts)
   → Suspend account, freeze balance and also investigate their ansestoral tree accordingly.to know if it is a ring in the linage an dfreeze accounts all these suspension intervals and caping must come from $settings->

4. User reports account as hacked
   → Immediate suspension, password reset required

5. Chargebacks/payment disputes (for card payments)
   → Suspend until resolved

Manual review required:

1. Unusual withdrawal patterns (large amounts after little activity)
2. VPN usage from high-risk countries
3. User disputes over commission distribution
4. Inconsistent personal information across forms

9. ADMIN DASHBOARD & CONTROLS
Key Metrics Display (Real-time)
Platform Overview:
├── Total registered users: 47,382
├── Active users (completed task in last 7 days): 23,195
├── Pending activation payments: ₦4,500,000
├── Total platform balance: ₦18,200,000
├── Pending withdrawal requests: ₦12,800,000
├── Burn rate: 0.85 (CAUTION status)
├── New registrations today: 1,247
├── Total paid out (lifetime): ₦156,000,000
├── Platform profit (lifetime): ₦89,000,000

User Acquisition:
├── Organic sign-ups: 320 today
├── Referral sign-ups: 927 today
├── Conversion rate (registration → activation): 67%
├── Average time to first referral: 3.2 days

Engagement Metrics:
├── Average tasks completed per user/day: 5.3
├── Daily active users (DAU): 12,450
├── Monthly active users (MAU): 38,900
├── Retention rate (30-day): 41%

Top Performers:
├── Highest earner this month: {name} - ₦487,000
├── Largest team: {name} - 2,384 downlines
├── Most referrals today: {name} - 23 direct
Admin Actions
User Management:
Search user: By name, phone, email, referral code, user ID

User profile view:
├── Personal info
├── Activation details (date, amount, plan type)
├── Current balances (pending + withdrawable)
├── Total earned (lifetime)
├── Total withdrawn (lifetime)
├── Referral tree visualization
├── Task completion history
├── Withdrawal history
├── Device fingerprints
├── Fraud flags (if any)

Available actions:
├── [Adjust Balance] - Manually add/subtract funds
├── [Suspend Account] - Temporarily disable
├── [Ban Account] - Permanent ban
├── [Reset Password] - Security issues
├── [Verify KYC] - Approve documents manually
├── [View Referral Network] - See entire downline tree
├── [Send Direct Message] - In-app notification
├── [Waive Withdrawal Minimum] - Special cases
Financial Controls:
Withdrawal Management:
├── View pending requests (sortable by priority, amount, date)
├── Bulk approve (select multiple, approve all)
├── Batch reject (with reason template)
├── Export to CSV (for manual bank transfers)
├── Mark as completed (after manual transfer)

Task Management:
├── Create new task template
├── Edit existing task (change reward, questions)
├── Activate/deactivate tasks
├── View task completion stats
├── Flag suspicious task completion patterns

Commission Adjustment:
├── Modify global commission rates (dangerous, affects all users)
├── Create temporary commission boost events
├── View commission payout history by level

Platform Controls:
├── Enable/disable new registrations
├── Enable/disable withdrawals (emergency stop)
├── Enable/disable referral bonuses
├── Adjust withdrawal processing times
├── Set maintenance mode (freeze all activity)
Content Management:
Announcements:
├── Create banner messages (top of dashboard)
├── Schedule notifications (send to all users or specific ranks)
├── Manage WhatsApp group announcements

Testimonials:
├── Upload success story videos
├── Curate withdrawal screenshots for marketing
├── Feature top earners on homepage

Marketing:
├── Generate referral competition leaderboards
├── Create bonus campaigns ("Double referral weekend!")
├── Design rank upgrade promotions

END OF BLUEPRINT


  