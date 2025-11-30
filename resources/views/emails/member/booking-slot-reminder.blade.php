@component('emails.layouts.branded')
    <p style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 20px;">
        Hey {{ $bookingSlot->booking->member->name ? explode(' ', $bookingSlot->booking->member->name)[0] : 'there' }},
    </p>

    <p style="margin-bottom: 16px;">
        This is a friendly reminder that you have a training session coming up tomorrow!
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
        {{-- Date --}}
        <tr>
            <td style="padding: 0 0 8px 0;">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding-right: 8px; vertical-align: middle;">
                            <img src="{{ asset('images/email-icons/calendar-outline-24.svg') }}"
                                 width="28" height="28"
                                 alt="Calendar"
                                 style="display: block; width: 28px; height: 28px;">
                        </td>
                        <td style="vertical-align: middle;">
                            <p style="margin: 0; font-size: 16px; font-weight: 500; color: #10b981;">
                                {{ $bookingSlot->start_time->format('l, F j') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- Time --}}
        <tr>
            <td style="padding: 0 0 8px 0;">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding-right: 8px; vertical-align: middle;">
                            <img src="{{ asset('images/email-icons/clock-outline-24.svg') }}"
                                 width="28" height="28"
                                 alt="Clock"
                                 style="display: block; width: 28px; height: 28px;">
                        </td>
                        <td style="vertical-align: middle;">
                            <p style="margin: 0; font-size: 16px; font-weight: 500; color: #10b981;">
                                {{ $bookingSlot->start_time->format('g:i A') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- Trainer --}}
        <tr>
            <td style="padding: 0;">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding-right: 8px; vertical-align: middle;">
                            @if($bookingSlot->booking->trainer->profile_photo_url)
                                <img src="{{ $bookingSlot->booking->trainer->profile_photo_url }}"
                                     width="28" height="28"
                                     alt="{{ $bookingSlot->booking->trainer->name }}"
                                     style="display: block; width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                            @else
                                <div style="display: inline-block; width: 28px; height: 28px; border-radius: 50%; background-color: #1f2937; color: white; text-align: center; line-height: 28px; font-weight: 600; font-size: 14px;">
                                    {{ strtoupper(substr($bookingSlot->booking->trainer->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            <p style="margin: 0; font-size: 16px; font-weight: 500; color: #10b981;">
                                {{ $bookingSlot->booking->trainer->name }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
        <tr>
            <td style="border-left: 4px solid #1f2937; background-color: #f3f4f6; padding: 12px; border-radius: 4px;">
                <p style="margin: 0; font-size: 16px; font-style: italic; color: #374151;">
                    {{ $motivationalMessage }}
                </p>
            </td>
        </tr>
    </table>

    <p style="margin-bottom: 4px; color: #374151;">
        Here to support you,
    </p>
    <p style="font-weight: 600; color: #111827;">
        The Lift Station Team
    </p>
@endcomponent
