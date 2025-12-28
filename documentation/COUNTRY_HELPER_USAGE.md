# CountryHelper Usage Guide

Dynamic country and currency management using `world-countries.json`

## Features

- ✅ 250+ countries with currency data
- ✅ Currency codes, names, and symbols
- ✅ Base64 encoded flags for UI
- ✅ Automatic currency formatting
- ✅ Zero hardcoded values

---

## Installation

The `world-countries.json` file is located at:
```
public/countries/world-countries.json
```

---

## Basic Usage

### Get Currency Symbol

```php
use App\Helpers\CountryHelper;

// Get currency symbol from country code
$symbol = CountryHelper::getCurrencySymbol('NGA'); // ₦
$symbol = CountryHelper::getCurrencySymbol('USA'); // $
$symbol = CountryHelper::getCurrencySymbol('GBR'); // £
$symbol = CountryHelper::getCurrencySymbol('GHA'); // ₵
```

### Get Currency Code

```php
$code = CountryHelper::getCurrencyCode('NGA'); // NGN
$code = CountryHelper::getCurrencyCode('KEN'); // KES
$code = CountryHelper::getCurrencyCode('ZAF'); // ZAR
```

### Format Money

```php
// With country code specified
$formatted = CountryHelper::formatMoney(5000, 'NGA'); // ₦5,000.00
$formatted = CountryHelper::formatMoney(1500, 'USA'); // $1,500.00
$formatted = CountryHelper::formatMoney(2000, 'GHA'); // ₵2,000.00

// Without country code (uses global_settings->country_of_operation)
$formatted = CountryHelper::formatMoney(10000); // ₦10,000.00 (if NGA is default)
```

### Get Full Currency Info

```php
$currency = CountryHelper::getCurrency('NGA');
/*
Returns:
[
    'code' => 'NGN',
    'name' => 'Naira',
    'symbol' => '₦'
]
*/
```

### Get Country Flag

```php
// Get base64 encoded flag image
$flag = CountryHelper::getFlag('NGA'); // Returns base64 PNG string

// Use in HTML
<img src="data:image/png;base64,{{ $flag }}" alt="Nigeria Flag" />
```

### Get Country Data

```php
// Get all country data
$country = CountryHelper::getByCode('NG'); // Using Alpha-2 code
$country = CountryHelper::getByAlpha3('NGA'); // Using Alpha-3 code

/*
Returns:
[
    'id' => 156,
    'name' => 'Nigeria',
    'isoAlpha2' => 'NG',
    'isoAlpha3' => 'NGA',
    'isoNumeric' => 566,
    'currency' => [
        'code' => 'NGN',
        'name' => 'Naira',
        'symbol' => '₦'
    ],
    'flag' => 'iVBORw0KGgoAAAA...' // Base64 image
]
*/
```

---

## Usage in Models

### Withdrawal Model Example

```php
use App\Helpers\CountryHelper;

public static function canUserWithdraw(User $user, float $amount): array
{
    $settings = GlobalSetting::first();
    $platformCountry = $settings->country_of_operation ?? 'NGA';

    $errors = [];

    // Dynamic currency in error messages
    if ($amount < $minAmount) {
        $errors[] = "Minimum withdrawal is " . CountryHelper::formatMoney($minAmount, $platformCountry);
    }

    return ['can_withdraw' => empty($errors), 'errors' => $errors];
}
```

### UserObserver Example

```php
use App\Helpers\CountryHelper;

public function created(User $user): void
{
    // Auto-detect currency from user's country
    $currency = CountryHelper::getCurrencyCode($user->country);

    Wallet::create([
        'user_id' => $user->id,
        'currency' => $currency, // NGN, GHS, KES, etc.
    ]);
}
```

---

## Usage in Controllers/Views

### Controller Example

```php
use App\Helpers\CountryHelper;

public function getCountries()
{
    // Get all countries for dropdown
    $countries = CountryHelper::forDropdown();

    /*
    Returns:
    [
        ['code' => 'NGA', 'name' => 'Nigeria', 'currency' => 'NGN', 'symbol' => '₦', 'flag' => '...'],
        ['code' => 'GHA', 'name' => 'Ghana', 'currency' => 'GHS', 'symbol' => '₵', 'flag' => '...'],
        ...
    ]
    */

    return response()->json($countries);
}
```

### Blade/Inertia Example

```php
// In Controller
return Inertia::render('Dashboard', [
    'balance' => CountryHelper::formatMoney($user->wallet->withdrawable_balance),
    'countries' => CountryHelper::forDropdown(),
]);
```

```vue
<!-- In Vue Component -->
<template>
  <div>
    <p>Available Balance: {{ balance }}</p>

    <select v-model="selectedCountry">
      <option v-for="country in countries" :key="country.code" :value="country.code">
        <img :src="`data:image/png;base64,${country.flag}`" class="inline w-4 h-4" />
        {{ country.name }} ({{ country.currency }})
      </option>
    </select>
  </div>
</template>
```

---

## Global Settings Integration

The platform currency is set in `global_settings` table:

```sql
UPDATE global_settings
SET country_of_operation = 'NGA' -- Nigeria
WHERE id = 1;
```

Admin can change this from the dashboard, and all currency formatting automatically updates.

---

## Supported Countries (Examples)

| Country | Code | Currency Code | Symbol |
|---------|------|---------------|--------|
| Nigeria | NGA | NGN | ₦ |
| Ghana | GHA | GHS | ₵ |
| Kenya | KEN | KES | KSh |
| South Africa | ZAF | ZAR | R |
| United States | USA | USD | $ |
| United Kingdom | GBR | GBP | £ |
| Canada | CAN | CAD | CA$ |

**Total: 250+ countries available**

---

## Benefits

1. **Admin Flexibility** - Change platform currency without code changes
2. **Multi-country Support** - Expand to new countries instantly
3. **Consistent Formatting** - All money displays use same helper
4. **No Hardcoding** - Zero hardcoded ₦, $, or £ symbols in codebase
5. **Flag Support** - Ready for multi-country UI with flags

---

## Best Practices

1. **Always use CountryHelper for money**
   ```php
   // ❌ Bad
   $message = "Minimum: ₦" . number_format($amount, 2);

   // ✅ Good
   $message = "Minimum: " . CountryHelper::formatMoney($amount);
   ```

2. **Let global_settings control default country**
   ```php
   // ❌ Bad
   $currency = 'NGN';

   // ✅ Good
   $currency = CountryHelper::getCurrencyCode(
       GlobalSetting::first()->country_of_operation
   );
   ```

3. **Use Alpha-3 codes (NGA, GHA) not Alpha-2 (NG, GH)**
   ```php
   // ✅ Preferred
   CountryHelper::getCurrencySymbol('NGA');

   // ✅ Also works
   CountryHelper::getCurrencySymbol('NG');
   ```

---

## File Structure

```
app/
├── Helpers/
│   └── CountryHelper.php
public/
└── countries/
    └── world-countries.json (498KB)
```

---

## Performance

- JSON file loaded once and cached in memory
- Subsequent calls use cached data
- No database queries needed
- Minimal performance impact
