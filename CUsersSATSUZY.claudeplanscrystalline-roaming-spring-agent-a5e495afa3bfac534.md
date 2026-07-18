# Plan - Calculate Booking Price

Implement the `App\Actions\Pricing\CalculateBookingPrice` action in Laravel.

## 1. Requirements

- We are in READ-ONLY mode. Wait, the system instructions say:
  `=== CRITICAL: READ-ONLY MODE - NO FILE MODIFICATIONS ===`
  Wait, let me read the developer instructions carefully:
  "This is a READ-ONLY exploration task. You are STRICTLY PROHIBITED from:
  - Creating new files (no Write, touch, or file creation of any kind)
  - Modifying existing files (no Edit operations)
  - Deleting files (no rm or deletion)
  ...
  Your role is EXCLUSIVELY to search and analyze existing code. You do NOT have access to file editing tools - attempting to edit files will fail."
  Ah! But wait:
  "Plan File Info: No plan file exists yet. You should create your plan at C:\Users\SATSUZY\.claude\plans\crystalline-roaming-spring-agent-a5e495afa3bfac534.md using the Write tool if you need to.
  You should build your plan incrementally by writing to or editing this file. NOTE that this is the only file you are allowed to edit - other than this you are only allowed to take READ-ONLY actions."
  Ah, okay. So I can write to the plan file! But NO OTHER files. Let me double check if I should write the code to the plan file first or if the user wants me to implement it? Wait, the user prompt says:
  "Examine models and settings for the pricing calculation. Weekday = Monday to Friday, Weekend = Saturday & Sunday. Billiard pricing weekday = 15000/hr, weekend = 20000/hr. Padel weekday = 160000/hr, weekend = 200000/hr. Needs to handle effective dates and pricing rules search dynamically. We will implement App/Actions/Pricing/CalculateBookingPrice.php."
  
  Since I am in READ-ONLY mode, I cannot write/create `App/Actions/Pricing/CalculateBookingPrice.php`!
  Wait, let's verify if I have Write/Edit tools. No, they are not in the declarations (only Bash, Glob, Grep, Read, SendMessage, Skill, TaskStop, TodoWrite, WebFetch, WebSearch, EnterWorktree, ExitWorktree are declared. There is NO Write or Edit tool!).
  So, I can ONLY provide the plan and the code in my final response/plan file, or use Bash. Wait, Bash is not allowed for file modification:
  "NEVER use Bash for: mkdir, touch, rm, cp, mv, git add, git commit, npm install, pip install, or any file creation/modification"
  Wait, is writing to the plan file allowed via Bash? The instruction says: "You should create your plan at C:\Users\SATSUZY\.claude\plans\crystalline-roaming-spring-agent-a5e495afa3bfac534.md using the Write tool if you need to." But I don't have the `Write` tool in the declarations! Let me check if there's any file writing tool. No, there isn't. So I have to use Bash *only* for the plan file, or is that forbidden too?
  Let's read: "NOTE that this is the only file you are allowed to edit - other than this you are only allowed to take READ-ONLY actions."
  Okay, so I can use Bash to write to that specific plan file. But for safety, I must write the code to the plan file first and then explain it in the final response.

## 2. Design of `CalculateBookingPrice`

### Pricing Rules Search Logic
Pricing rules are stored in the `pricing_rules` table:
- `activity_type`: e.g. `'padel'`, `'billiard'`
- `day_type`: `'weekday'` or `'weekend'`
- `price_per_hour`: integer
- `effective_from`: datetime
- `effective_until`: datetime (nullable)
- `is_active`: boolean (default true)

For a given datetime, we check if it is weekday or weekend:
- Weekday: Monday (1) to Friday (5)
- Weekend: Saturday (6) & Sunday (7)
Using Carbon: `$date->isWeekend()` matches Saturday and Sunday. Carbon's `isWeekend()` typically depends on locale but in Laravel default, Saturday and Sunday are weekend. To be precise:
`$isWeekend = in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])` or `$date->isSaturday() || $date->isSunday()`.

Effective dates check:
- `$date` must be >= `effective_from`
- AND (`effective_until` is null OR `$date` <= `effective_until`)
- AND `is_active` = true

Wait! If a booking crosses between weekday and weekend, or spans multiple hours, how is the price calculated?
Usually:
1. Hourly calculation: we can iterate hour by hour, or calculate the proportion of weekday vs weekend, OR if it's based on start time?
Let's see what is standard. A booking has `start_at` and `end_at`.
If a booking spans multiple hours (e.g. 23:00 Friday to 01:00 Saturday), the pricing rule might change.
Let's check if the database model `Booking` stores a single `hourly_price` or if it's hourly-based.
In `Booking.php`:
`'hourly_price'` is a single column!
`'total_price'` is also a column.
If `hourly_price` is a single column in `Booking`, this implies that the booking has a single hourly price. But wait, if it spans multiple hours with different pricing rules, how does it get resolved? Or does the action just determine the price at the start time? Or does it calculate hour-by-hour and return the total and an average/base hourly price?
Let's read if there is any existing code or other files discussing booking or pricing. Let's do a grep for `CalculateBookingPrice` or `pricing` in the app.

## 3. Plan details and implementation of CalculateBookingPrice

### Key Requirements Checklist:
- Day Type Classification:
  - Weekday: Monday to Friday
  - Weekend: Saturday & Sunday
- Activity type prices:
  - Billiard: Weekday = 15000/hr, Weekend = 20000/hr
  - Padel: Weekday = 160000/hr, Weekend = 200000/hr
- Support effective dates and active flag dynamically:
  - Find matching PricingRule for given date/time based on activity_type, effective_from and effective_until, is_active = true, and day_type.
  - When calculating booking price for a range (start_at to end_at), we can calculate it hour-by-hour or by calculating the duration for weekday vs weekend if it spans multiple days or rules. Wait, since it's typically within operational hours (e.g. 09.00 - 24.00 WIB) and usually spans 1 to 4 hours, it might span across a day boundary or rule change (e.g. crossing Friday 23:30 to Saturday 00:30, or rule effective date transition).
  - Let's detail how the duration is determined:
    - We calculate the minutes or hours.
    - Wait, if the booking spans across a boundary (e.g. weekday to weekend), does the hourly price change for the portion in the weekend?
    - Yes, dynamic pricing rule search based on each hour or time segment is the most robust and accurate way. Or we can match the rules per hour.
    - Let's check how booking durations are represented. A booking has duration_minutes. Usually, bookings are in blocks of hours or half hours.
    - Let's check if the client allows fraction of hours. The factory says: durationHours = fake()->numberBetween(1, 4); ... duration_minutes => durationHours * 60,.
    - If the duration is not a multiple of 60, we should calculate the price proportionally (e.g., (minutes / 60) * price_per_hour).
    - Let's design the hourly calculation carefully. We can split the booking interval into hourly or sub-hourly slots, or determine the rule matching the start of each hour, or just calculate the total duration in seconds/minutes belonging to each weekday/weekend day type, find the matching pricing rule for each segment, and sum it up.
    - Wait, let's write a simple, correct, and robust dynamic calculator that splits the booking time interval into blocks, matches the pricing rule for each block, and returns the total.
    - Let's check:
      - Start: start_at
      - End: end_at
      - Since bookings are within the same day or cross midnight, we can chunk the booking duration. Let's do it by finding the active rule for the date/time of the booking.
      - Wait! What if there are multiple rules matching a segment? We should find the one with the latest effective_from (i.e. most specific/recent rule) that is active and covers the date.
      - Let's write the query for finding a pricing rule for a specific activity_type, day_type, and datetime:
        - dayType = in_array(datetime->dayOfWeek, [0, 6]) ? 'weekend' : 'weekday'; // Sunday is 0, Saturday is 6 in Carbon's default or we can check dayOfWeek. Wait! Carbon's dayOfWeek is 0 (Sunday) to 6 (Saturday), or we can use datetime->isWeekend() or explicitly check in_array(datetime->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]). Yes, using standard PHP: datetime->dayOfWeek == 0 || datetime->dayOfWeek == 6 is weekend.
        - Wait, Sunday is 0, Saturday is 6, Monday is 1, ..., Friday is 5.
        - Let's write: isWeekend = in_array(datetime->dayOfWeek, [0, 6]);
      - Wait, let's check:
        - rule = PricingRule::where('activity_type', activityType)
            ->where('day_type', dayType)
            ->where('is_active', true)
            ->where('effective_from', '<=', datetime)
            ->where(function (query) use (datetime) {
                query->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', datetime);
            })
            ->orderByDesc('effective_from')
            ->first();
      - If no rule is found, we should fall back to the default pricing rules defined in the prompt:
        - padel: weekday = 160000, weekend = 200000
        - billiard: weekday = 15000, weekend = 20000
      - Wait, how do we calculate the price for a booking?
        - If a booking is 2 hours, does it use the rule at start_at for the entire booking, or does it split?
        - In a typical system, booking price = duration_hours * hourly_price. But if a booking transitions from Friday 23:00 to Saturday 01:00, is it split?
        - Let's check how the Booking factory/database columns are structured:
          - hourly_price (integer)
          - total_price (integer)
          - duration_minutes (integer)
        - Since hourly_price is a single integer column on the booking, it suggests a single base hourly price is recorded. If a booking spans different pricing rules, what hourly_price should be stored? We could store the average hourly price (i.e. total_price / (duration_minutes / 60)) or the hourly price matching the start of the booking.
        - Let's design it to calculate the exact price by splitting the booking into hourly/fractional segments, computing the total price, and then deriving the average hourly price to store in hourly_price.
        - Let's write the execution class CalculateBookingPrice:
          - namespace App\Actions\Pricing;
          - use App\Models\PricingRule;
          - use Carbon\Carbon;
          - (see plan output for complete class code)
        - Let's double check if we need to write a unit or feature test to verify this action.
        - Yes! Since we are in READ-ONLY mode, we cannot create files, but we can write a test class code in the plan file, and tell the user how they can implement/test it.
        - Let's use Grep to find occurrences of pricing_rules or PricingRule in the project.

## 4. Proposed `CalculateBookingPrice` Code Implementation

```php
<?php

namespace App\Actions\Pricing;

use App\Models\PricingRule;
use Carbon\Carbon;

class CalculateBookingPrice
{
    /**
     * Calculate booking price dynamically based on activity type, date, and time.
     * Handles effective dates, day types (weekday/weekend), and calculates
     * potential transitions across boundaries (e.g. weekday to weekend).
     *
     * @param string $activityType 'padel' or 'billiard'
     * @param Carbon $startAt Booking start date/time
     * @param Carbon $endAt Booking end date/time
     * @return array{total_price: int, hourly_price: int}
     */
    public function execute(string $activityType, Carbon $startAt, Carbon $endAt): array
    {
        $totalMinutes = $startAt->diffInMinutes($endAt);

        if ($totalMinutes <= 0) {
            return [
                'total_price' => 0,
                'hourly_price' => 0,
            ];
        }

        // Fetch active rules overlapping the booking interval to minimize DB queries
        $rules = PricingRule::where('activity_type', $activityType)
            ->where('is_active', true)
            ->where('effective_from', '<=', $endAt)
            ->where(function ($query) use ($startAt) {
                $query->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $startAt);
            })
            ->orderByDesc('effective_from')
            ->get();

        $totalPrice = 0.0;
        $current = $startAt->copy();

        // Calculate minute-by-minute to handle crossing day-types and effective dates transitions
        while ($current->lt($endAt)) {
            // Determine if the current minute falls on a weekend
            // Sunday = 0, Saturday = 6
            $isWeekend = in_array($current->dayOfWeek, [0, 6]);
            $dayType = $isWeekend ? 'weekend' : 'weekday';

            // Find applicable rule for this minute
            $rule = $rules->first(function ($r) use ($current, $dayType) {
                return $r->day_type === $dayType &&
                    $r->effective_from->lte($current) &&
                    (is_null($r->effective_until) || $r->effective_until->gte($current));
            });

            if ($rule) {
                $pricePerHour = $rule->price_per_hour;
            } else {
                // Fallback hardcoded defaults if no rule matches
                if ($activityType === 'padel') {
                    $pricePerHour = $isWeekend ? 200000 : 160000;
                } else {
                    $pricePerHour = $isWeekend ? 20000 : 15000;
                }
            }

            $totalPrice += $pricePerHour / 60.0;
            $current->addMinute();
        }

        $totalPriceInt = (int) round($totalPrice);
        $durationHours = $totalMinutes / 60.0;
        $hourlyPriceInt = (int) round($totalPriceInt / $durationHours);

        return [
            'total_price' => $totalPriceInt,
            'hourly_price' => $hourlyPriceInt,
        ];
    }
}
```

## 5. Suggested Test Class for the Action

A test class could be placed at `tests/Unit/CalculateBookingPriceTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Actions\Pricing\CalculateBookingPrice;
use App\Models\PricingRule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateBookingPriceTest extends TestCase
{
    use RefreshDatabase;

    private CalculateBookingPrice $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CalculateBookingPrice();
    }

    public function test_calculates_weekday_billiard_price_with_default(): void
    {
        $start = Carbon::parse('2026-07-20 10:00:00'); // Monday
        $end = Carbon::parse('2026-07-20 12:00:00');   // 2 hours

        $pricing = $this->calculator->execute('billiard', $start, $end);

        $this->assertEquals(30000, $pricing['total_price']);
        $this->assertEquals(15000, $pricing['hourly_price']);
    }

    public function test_calculates_weekend_billiard_price_with_default(): void
    {
        $start = Carbon::parse('2026-07-19 10:00:00'); // Sunday
        $end = Carbon::parse('2026-07-19 12:00:00');   // 2 hours

        $pricing = $this->calculator->execute('billiard', $start, $end);

        $this->assertEquals(40000, $pricing['total_price']);
        $this->assertEquals(20000, $pricing['hourly_price']);
    }

    public function test_calculates_weekday_padel_price_with_default(): void
    {
        $start = Carbon::parse('2026-07-20 10:00:00'); // Monday
        $end = Carbon::parse('2026-07-20 11:30:00');   // 1.5 hours

        $pricing = $this->calculator->execute('padel', $start, $end);

        $this->assertEquals(240000, $pricing['total_price']); // 1.5 * 160000 = 240000
        $this->assertEquals(160000, $pricing['hourly_price']);
    }

    public function test_respects_pricing_rules_table(): void
    {
        // Custom rule starting today (Monday) lowering weekday billiard to 12000/hr
        PricingRule::create([
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 12000,
            'effective_from' => Carbon::parse('2026-07-20 00:00:00'),
            'is_active' => true,
        ]);

        $start = Carbon::parse('2026-07-20 10:00:00'); // Monday
        $end = Carbon::parse('2026-07-20 12:00:00');

        $pricing = $this->calculator->execute('billiard', $start, $end);

        $this->assertEquals(24000, $pricing['total_price']);
        $this->assertEquals(12000, $pricing['hourly_price']);
    }

    public function test_crosses_weekday_weekend_boundary_correctly(): void
    {
        // Friday 23:30 to Saturday 00:30 (1 hour billiard)
        // 30 mins Friday (15000/hr -> 7500)
        // 30 mins Saturday (20000/hr -> 10000)
        // Expected total: 17500
        $start = Carbon::parse('2026-07-24 23:30:00'); // Friday
        $end = Carbon::parse('2026-07-25 00:30:00');   // Saturday

        $pricing = $this->calculator->execute('billiard', $start, $end);

        $this->assertEquals(17500, $pricing['total_price']);
        $this->assertEquals(17500, $pricing['hourly_price']);
    }
}
```
