@component('emails.layouts.branded')
    <p style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 20px;">
        Hi {{ $member->name ? explode(' ', $member->name)[0] : 'there' }},
    </p>

    <p style="margin-bottom: 16px;">
        Welcome to LiftStation! We're thrilled to have you join our fitness family.
    </p>

    <p style="margin-bottom: 16px;">
        Over here, we don't just count reps—we <strong>make every rep count</strong>. Your journey to becoming stronger, healthier, and more confident starts now, and we're here to support you every step of the way.
    </p>

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
