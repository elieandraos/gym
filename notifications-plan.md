# Member Notification Preferences — Implementation Plan

## Goal

Add per-member channel preferences (email, WhatsApp) respected by all notification dispatch points.

---

## Context

Two places currently send notifications to members with no preference check:

1. `SendWelcomeEmailToMember` action → called from `CreateMember`
2. `SendBookingSlotReminders` console command → runs daily at 21:00

`User` model already has the `Notifiable` trait. No `app/Notifications/` directory exists yet.

---

## Approach: Laravel Notifications

Each event gets a Notification class with a `via()` method that reads member settings to decide which channels fire. The dispatch points (`action`, `command`) just call `$member->notify()` — no `if` guards scattered around.

---

## Step 1 — Add Member defaults to `HasSettings`

**File:** `app/Traits/HasSettings.php`

In `getDefaultSettings()`, add member defaults:

```php
'Member' => [
    'notifications' => [
        'receive_email'     => true,
        'receive_whatsapp'  => false,
    ],
],
```

---

## Step 2 — Create `app/Notifications/Member/WelcomeMember.php`

```php
<?php

namespace App\Notifications\Member;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class WelcomeMember extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @return array<string> */
    public function via(User $notifiable): array
    {
        $channels = [];

        if ($notifiable->getSetting('notifications.receive_email', true)) {
            $channels[] = 'mail';
        }

        if ($notifiable->getSetting('notifications.receive_whatsapp', false)) {
            $channels[] = 'whatsapp'; // custom channel — wire up later
        }

        return $channels;
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Lift Station! 🎉')
            ->view('emails.member.welcome', ['member' => $notifiable]);
    }
}
```

---

## Step 3 — Create `app/Notifications/Member/BookingSlotReminder.php`

```php
<?php

namespace App\Notifications\Member;

use App\Mail\Member\BookingSlotReminderEmail;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class BookingSlotReminder extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public BookingSlot $bookingSlot) {}

    /** @return array<string> */
    public function via(User $notifiable): array
    {
        $channels = [];

        if ($notifiable->getSetting('notifications.receive_email', true)) {
            $channels[] = 'mail';
        }

        if ($notifiable->getSetting('notifications.receive_whatsapp', false)) {
            $channels[] = 'whatsapp';
        }

        return $channels;
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your training session is tomorrow!')
            ->view('emails.member.booking-slot-reminder', [
                'member'           => $notifiable,
                'bookingSlot'      => $this->bookingSlot,
                'motivationalMessage' => BookingSlotReminderEmail::getRandomMotivationalMessage(),
            ]);
    }
}
```

---

## Step 4 — Update `app/Actions/Admin/SendWelcomeEmailToMember.php`

Replace:
```php
Mail::to($member->email)->queue(new WelcomeEmail($member));
```
With:
```php
$member->notify(new WelcomeMember());
```

---

## Step 5 — Update `app/Console/Commands/SendBookingSlotReminders.php`

Replace:
```php
Mail::to($bookingSlot->member->email)->queue(new BookingSlotReminderEmail($bookingSlot));
```
With:
```php
$bookingSlot->member->notify(new BookingSlotReminder($bookingSlot));
```

---

## Files to Modify / Create

| File | Action |
|------|--------|
| `app/Traits/HasSettings.php` | Add Member notification defaults |
| `app/Notifications/Member/WelcomeMember.php` | Create |
| `app/Notifications/Member/BookingSlotReminder.php` | Create |
| `app/Actions/Admin/SendWelcomeEmailToMember.php` | Swap `Mail::queue()` → `notify()` |
| `app/Console/Commands/SendBookingSlotReminders.php` | Swap `Mail::queue()` → `notify()` |

---

## Tests to Write / Update

Use `Notification::fake()` in all notification tests.

**WelcomeMember:**
- Default settings → `mail` channel dispatched
- `receive_email = false` → `mail` not dispatched
- `receive_whatsapp = true` → `whatsapp` channel dispatched

**BookingSlotReminder:**
- Default settings → `mail` channel dispatched for tomorrow's slot
- `receive_email = false` → not dispatched on `mail`
- Existing command tests (filter, status, logging) should still pass

```bash
php artisan test --compact --filter=CreateMember
php artisan test --compact --filter=BookingSlotReminder
```

---

## Future: Adding WhatsApp

1. Install a WhatsApp notification channel package (e.g. Twilio)
2. Add `toWhatsapp()` method to each Notification class
3. No changes needed to `via()`, commands, or actions