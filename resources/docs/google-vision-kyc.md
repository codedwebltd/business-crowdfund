# Google Vision API - KYC Auto-Verification

## Quick Start

### Replace JSON File (Billing-Enabled Account)

Simply replace:
```bash
/home/qiviotalk/business.qiviotalk.online/storage/app/google-vision.json
```

No code changes needed! ✓

## Setup Steps

### 1. Create Google Cloud Project
- Go to: https://console.cloud.google.com/
- Click "Create Project"
- Name it

### 2. Enable Vision API
- Search "Cloud Vision API"
- Click "Enable"

### 3. Enable Billing ⚠️ REQUIRED
- Visit: https://console.developers.google.com/billing/enable
- Add payment method
- **FREE**: First 1,000 requests/month
- **Cost**: $1.50 per 1,000 after that

### 4. Create Service Account
- IAM & Admin → Service Accounts
- Create: "kyc-verification"
- Role: "Cloud Vision AI Service Agent"

### 5. Download JSON Key
- Click service account → Keys tab
- Add Key → JSON
- Download file

### 6. Replace JSON on Server
```bash
# Upload and save as:
storage/app/google-vision.json
chmod 600 google-vision.json
```

### 7. Start Queue Worker
```bash
php artisan queue:work --tries=3
```

## How It Works

```
User Submits KYC → Job Dispatched → Queue Processes →
Vision API Analyzes → Auto-Approve if 70%+ → Notify User
```

## Files
- `app/Services/VisionService.php` - Vision API client
- `app/Jobs/VerifyKycDocuments.php` - Background job
- `app/Http/Controllers/User/KycController.php` - Dispatches job

## Monitoring
```bash
tail -f storage/logs/laravel.log
```
