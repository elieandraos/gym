<?php

namespace App\Mail\Member;

use App\Models\BookingSlot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingSlotReminderEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $motivationalMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public BookingSlot $bookingSlot,
    ) {
        $this->motivationalMessage = $this->getRandomMotivationalMessage();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your training session is tomorrow!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member.booking-slot-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get a random motivational message.
     */
    private function getRandomMotivationalMessage(): string
    {
        $messages = [
            'The iron is calling your name. Time to answer with strength!',
            'Every rep tomorrow brings you closer to the best version of yourself.',
            'Champions are made in training sessions like the one you have tomorrow. Be ready!',
            'Your future self will thank you for showing up tomorrow. Let\'s go!',
            'The only bad workout is the one that didn\'t happen. See you tomorrow!',
            'Tomorrow you\'ll be one step closer to your goals. Make it count!',
            'Strength doesn\'t come from what you can do. It comes from overcoming what you thought you couldn\'t.',
            'Your body can stand almost anything. Tomorrow, it\'s your mind you need to convince!',
            'Don\'t stop when you\'re tired tomorrow. Stop when you\'re done!',
            'Your dedication is inspiring. Tomorrow, let\'s push those limits even further!',
            'Great things never come from comfort zones. Tomorrow, we step outside yours!',
            'The harder the battle tomorrow, the sweeter the victory. Bring your A-game!',
            'Another day, another opportunity to get stronger. Let\'s do this tomorrow!',
            'Progress is built one session at a time. Tomorrow is your next brick!',
            'Winners train, losers complain. Tomorrow, let\'s train like winners!',
            'Success starts with showing up. We\'ll see you tomorrow, champ!',
            'The work you put in tomorrow will pay dividends for life. Let\'s invest!',
            'Tomorrow is another chance to prove that you\'re stronger than your excuses!',
            'Beast mode: loading... Get ready for tomorrow\'s session!',
            'Sweat is just fat crying. Let\'s make it weep tomorrow!',
            'Your tomorrow self is counting on your today commitment. Don\'t let them down!',
            'Muscles are torn in the gym, fed in the kitchen, and built in bed. Step one starts tomorrow!',
            'Small daily improvements lead to stunning results. Tomorrow, let\'s keep building!',
            'Consistency beats intensity. Tomorrow, let\'s stay consistent and crush it!',
        ];

        return $messages[array_rand($messages)];
    }
}
