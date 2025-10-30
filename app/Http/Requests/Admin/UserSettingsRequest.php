<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Calendar settings
            'calendar.default_trainer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', Role::Trainer->value)],
            'calendar.start_day' => ['sometimes', 'required', 'string', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'calendar.end_day' => ['sometimes', 'required', 'string', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'calendar.start_hour' => ['sometimes', 'required', 'integer', 'between:1,12'],
            'calendar.start_period' => ['sometimes', 'required', 'string', Rule::in(['AM', 'PM'])],
            'calendar.end_hour' => ['sometimes', 'required', 'integer', 'between:1,12'],
            'calendar.end_period' => ['sometimes', 'required', 'string', Rule::in(['AM', 'PM'])],

            // Notification settings
            'notifications.new_member_email_to_owners' => ['sometimes', 'required', 'boolean'],
            'notifications.owner_emails' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (empty($value)) {
                    return;
                }

                $emails = array_map('trim', explode(',', $value));

                foreach ($emails as $email) {
                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $fail("The email '$email' is not a valid email address.");

                        return;
                    }
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'calendar.default_trainer_id.exists' => 'The selected trainer does not exist.',
            'calendar.start_day.in' => 'The start day must be a valid day of the week.',
            'calendar.end_day.in' => 'The end day must be a valid day of the week.',
            'calendar.start_hour.between' => 'The start hour must be between 1 and 12.',
            'calendar.end_hour.between' => 'The end hour must be between 1 and 12.',
            'calendar.start_period.in' => 'The start period must be AM or PM.',
            'calendar.end_period.in' => 'The end period must be AM or PM.',
        ];
    }
}
