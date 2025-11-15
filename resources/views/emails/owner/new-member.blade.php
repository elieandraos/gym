@php use Carbon\Carbon; @endphp
@component('emails.layouts.branded')
    <p style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 20px;">
        Great news!
    </p>

    <p style="margin-bottom: 16px;">
        <strong>{{ $member->name }}</strong> just joined LiftStation!
    </p>

    <p style="margin-bottom: 24px; color: #6b7280;">
        Registration
        Date: {{ $member->registration_date ? Carbon::parse($member->registration_date)->format('F j, Y') : 'N/A' }}
    </p>

    <p style="margin-bottom: 24px;">
        This brings us one step closer to building our fitness community. Time to help them achieve their goals!
    </p>

    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center">
                            <a href="{{ route('admin.members.show', $member) }}"
                               target="_blank"
                               rel="noopener"
                               style="display: inline-block; background-color: #1f2937; color: #ffffff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 16px;">
                                View Member Profile
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin-top: 32px; margin-bottom: 4px; color: #374151;">
        See you on the floor,
    </p>
    <p style="font-weight: 600; color: #111827;">
        The LiftStation Team
    </p>
@endcomponent
