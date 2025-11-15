@component('emails.layouts.branded')
    <p style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 20px;">
        Hey {{ $member->name ? explode(' ', $member->name)[0] : 'there' }},
    </p>

    <p style="margin-bottom: 16px;">
        Welcome to LiftStation! We're thrilled to have you join our fitness family.
    </p>

    <p style="margin-bottom: 16px;">
        Your journey to becoming stronger, healthier, and more confident starts now, and we're here to support you every step of the way.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
        <tr>
            <td style="border-left: 4px solid #1f2937; background-color: #f3f4f6; padding: 20px; border-radius: 4px;">
                <p style="margin: 0; font-size: 16px; font-style: italic; color: #374151;">
                    ❝Over here we don't just count reps, we <strong style="color: #111827;">make every rep count</strong>❞
                </p>
            </td>
        </tr>
    </table>

    <p style="margin-bottom: 24px;">
        Let's achieve great things together!
    </p>

    <p style="margin-bottom: 4px; color: #374151;">
        See you on the floor,
    </p>
    <p style="font-weight: 600; color: #111827;">
        The LiftStation Team
    </p>
@endcomponent
