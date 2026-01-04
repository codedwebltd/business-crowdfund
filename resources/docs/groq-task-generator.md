# Groq AI Task Generator - Complete Setup Guide

## What is This?

The **Groq AI Task Generator** automatically creates tasks for your users using artificial intelligence. Instead of manually creating surveys, video tasks, and product reviews one by one, the AI generates hundreds of them for you!

### What It Creates:
- **📊 Surveys** - AI generates custom survey questions relevant to your country
- **🎥 Video Tasks** - Fetches trending YouTube videos and creates verification questions
- **⭐ Product Reviews** - Generates lists of popular products for users to review
- **📱 Sync Tasks** - Creates device data collection tasks (no AI needed)

---

## Step 1: Get Your Groq API Key (Free!)

Groq provides **FREE** AI API access that's faster than ChatGPT!

### Instructions:

1. **Visit Groq Console**
   - Go to: https://console.groq.com/

2. **Create Account (Free)**
   - Click "Sign Up" or "Get Started"
   - Sign up with Google, GitHub, or email
   - No credit card required! ✓

3. **Get Your API Key**
   - Once logged in, go to "API Keys" section
   - Click "Create API Key"
   - Give it a name like "TaskGenerator"
   - **Copy the key** - it looks like this: `gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
   - ⚠️ **IMPORTANT**: Save this key somewhere safe! You can't see it again!

### Free Tier Limits:
- **30 requests per minute** (plenty for task generation)
- **Free forever** for moderate usage
- No credit card required

---

## Step 2: Add Your Groq API Key to the Platform

### Option A: Through Admin Settings (Recommended)

1. **Login to Admin Panel**
   - Go to your admin dashboard

2. **Navigate to Settings**
   - Click on "Global Settings" or "AI & Task Generation"

3. **Paste Your API Key**
   - Find the "Groq API Key" field
   - Paste your key: `gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

4. **Save Settings**
   - Click "Save" or "Update Settings"

### Option B: Directly in Database (Advanced)

If you don't see the AI settings in your admin panel:

```bash
# Access your database
mysql -u your_username -p your_database_name

# Update the settings
UPDATE global_settings
SET ai_configuration = JSON_SET(
  ai_configuration,
  '$.groq_api_key',
  'gsk_your_actual_api_key_here'
)
WHERE id = 1;
```

---

## Step 3: Configure Task Generation Settings

### Enable Task Generation

In your admin settings, configure these options:

```json
{
  "groq_api_key": "gsk_your_key_here",
  "groq_endpoint": "https://api.groq.com/openai/v1/chat/completions",
  "groq_model": "llama-3.1-8b-instant",
  "temperature": 0.7,
  "ai_task_generation_enabled": true,
  "min_task_templates_threshold": 50
}
```

### What Each Setting Means:

| Setting | What It Does | Recommended Value |
|---------|-------------|-------------------|
| `groq_api_key` | Your Groq API key | `gsk_xxxxx...` |
| `groq_endpoint` | Groq API URL | `https://api.groq.com/openai/v1/chat/completions` |
| `groq_model` | AI model to use | `llama-3.1-8b-instant` (fastest, free) |
| `temperature` | Creativity level (0-1) | `0.7` (balanced) |
| `ai_task_generation_enabled` | Turn AI on/off | `true` |
| `min_task_templates_threshold` | Minimum tasks before auto-generation | `50` |

### Choose How Many Tasks to Generate

```json
{
  "tasks_to_generate": {
    "surveys": 10,
    "videos": 15,
    "syncs": 5,
    "reviews": 20
  }
}
```

This tells the AI to generate:
- **10 surveys** (e.g., "What's your favorite mobile network?")
- **15 video tasks** (trending YouTube videos with questions)
- **5 sync tasks** (device data collection)
- **20 product reviews** (e.g., "Review: Samsung Galaxy S24")

### Set Reward Amounts

Configure how much users earn for each task type:

```json
{
  "reward_ranges": {
    "survey": {"min": 30, "max": 100},
    "video": {"min": 150, "max": 400},
    "review": {"min": 50, "max": 80},
    "sync": {"fixed": 200}
  }
}
```

---

## Step 4: Get YouTube API Key (For Video Tasks)

Video tasks fetch trending YouTube videos and create questions. You need a YouTube API key:

### Instructions:

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/

2. **Create New Project**
   - Click "Select a project" → "New Project"
   - Name it "TaskGenerator" or anything you like
   - Click "Create"

3. **Enable YouTube Data API v3**
   - Go to "APIs & Services" → "Library"
   - Search for "YouTube Data API v3"
   - Click it → Click "Enable"

4. **Create API Key**
   - Go to "APIs & Services" → "Credentials"
   - Click "Create Credentials" → "API Key"
   - Copy your key: `AIzaSy...`

5. **Add to Settings**
   - In your admin panel, add this to AI Configuration:
   ```json
   {
     "youtube_api_key": "AIzaSy_your_youtube_key_here"
   }
   ```

### YouTube API Limits:
- **10,000 requests per day** (free)
- That's enough for ~1,000 video tasks per day!

---

## Step 5: Run Task Generation

### Automatic Generation (Recommended)

The system automatically generates tasks when:
- Active task count falls below your threshold (default: 50)
- A scheduled job runs (daily at 2 AM)

### Manual Generation (Testing)

To test if everything works:

```bash
# SSH into your server
cd /home/qiviotalk/business.qiviotalk.online

# Run task generation command
php artisan tasks:generate

# Check the output - you should see:
# ✓ Generated 10 surveys
# ✓ Generated 15 video tasks
# ✓ Generated 5 sync tasks
# ✓ Generated 20 reviews
```

### Setup Automatic Scheduling

Add this to your crontab to run daily:

```bash
# Edit crontab
crontab -e

# Add this line (runs at 2 AM daily)
0 2 * * * cd /home/qiviotalk/business.qiviotalk.online && php artisan schedule:run >> /dev/null 2>&1
```

---

## How It Works (Behind the Scenes)

### Survey Generation Process:

1. **AI Generates Questions**
   - System asks Groq: "Create a survey about technology usage in Nigeria"
   - Groq AI creates culturally relevant questions
   - Questions are tailored to your country

2. **Example AI Response**:
```json
{
  "title": "Mobile Banking Usage Survey",
  "description": "Share your mobile banking habits",
  "questions": [
    {
      "id": 1,
      "text": "Which mobile banking app do you use most?",
      "type": "single_choice",
      "options": ["OPay", "PalmPay", "Kuda", "GTBank"]
    },
    {
      "id": 2,
      "text": "How often do you transfer money?",
      "type": "single_choice",
      "options": ["Daily", "Weekly", "Monthly", "Rarely"]
    }
  ]
}
```

3. **Task Created**
   - System saves this as a task template
   - Admin must review and activate it
   - Once activated, users can complete it and earn rewards

### Video Task Generation:

1. **Fetch Trending Videos**
   - System calls YouTube API for trending videos in your country
   - Gets video ID, title, description, duration

2. **AI Creates Verification Questions**
   - Groq analyzes the video title and description
   - Creates questions to verify users actually watched it

3. **Example**:
   - Video: "iPhone 15 Pro Review - All New Features"
   - AI generates questions like:
     - "What phone model is being reviewed in this video?"
     - "What is the main topic of this video?"

---

## Troubleshooting

### Problem: "Groq API key not configured"

**Solution:**
- Double-check your API key is correct
- Make sure it starts with `gsk_`
- Verify it's saved in `ai_configuration` JSON

### Problem: "Failed to generate tasks"

**Check these:**
1. Is `ai_task_generation_enabled` set to `true`?
2. Do you have internet connection?
3. Check logs: `tail -f storage/logs/laravel.log`
4. Verify Groq API key is valid (test at console.groq.com)

### Problem: "YouTube API quota exceeded"

**Solution:**
- You've used all 10,000 free requests for today
- Wait until tomorrow, or
- Upgrade to paid quota (rarely needed)

### Problem: "No tasks appearing in admin panel"

**Solution:**
- Generated tasks are set to **inactive** by default
- Go to Admin → Task Templates
- Review and activate them manually

---

## Best Practices

### For Beginners:

1. **Start Small**
   - Generate 5-10 tasks first to test
   - Review the quality of AI-generated content
   - Adjust settings if needed

2. **Review Before Activating**
   - AI generates tasks as "inactive" for a reason
   - Always review before making them available to users
   - Check questions make sense for your country

3. **Monitor Costs**
   - Groq is free for moderate use
   - YouTube API: 10,000 requests/day free
   - You're unlikely to exceed free tiers

### For Advanced Users:

1. **Customize AI Prompts**
   - Edit prompts in `app/Services/GroqService.php`
   - Make questions more specific to your niche

2. **Adjust Temperature**
   - Lower (0.3-0.5): More consistent, predictable tasks
   - Higher (0.8-1.0): More creative, varied tasks

3. **Integrate with Webhooks**
   - Get notified when tasks are low
   - Auto-activate high-quality AI tasks

---

## Cost Breakdown

### FREE Forever:
- **Groq AI**: 30 requests/minute, unlimited tasks
- **YouTube API**: 10,000 requests/day = ~1,000 video tasks/day

### If You Exceed Free Tier:
- **Groq**: Very cheap, ~$0.10 per 1M tokens
- **YouTube**: ~$0.05 per 1,000 requests after quota

### Example Monthly Cost (Heavy Usage):
- 1,000 AI-generated tasks/day = **$0** (within free tier)
- Even if you exceed limits = less than **$5/month**

---

## Summary Checklist

✅ Get Groq API key from console.groq.com
✅ Add key to admin settings
✅ Get YouTube API key (optional, for video tasks)
✅ Configure task generation settings
✅ Set reward amounts for each task type
✅ Run test generation: `php artisan tasks:generate`
✅ Review generated tasks in admin panel
✅ Activate quality tasks for users
✅ Set up cron job for automatic daily generation

---

## Need Help?

If you're stuck, check:
- **Logs**: `storage/logs/laravel.log`
- **Groq Status**: https://status.groq.com/
- **YouTube API Limits**: https://console.cloud.google.com/apis/

The AI task generator saves you **hours of manual work** and creates fresh, engaging tasks automatically! 🚀
