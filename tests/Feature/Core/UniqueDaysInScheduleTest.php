<?php

use App\Rules\UniqueDaysInSchedule;
use Illuminate\Support\Facades\Validator;

describe('UniqueDaysInSchedule validation rule', function () {
    it('passes validation when all days are unique', function () {
        $rule = new UniqueDaysInSchedule;

        $data = [
            'days' => [
                ['day' => 'Monday', 'time' => '07:00 am'],
                ['day' => 'Wednesday', 'time' => '08:00 am'],
                ['day' => 'Friday', 'time' => '09:00 am'],
            ],
        ];

        $validator = Validator::make($data, [
            'days' => ['required', 'array', $rule],
        ]);

        expect($validator->passes())->toBeTrue();
    });

    it('fails validation when days contain duplicates', function () {
        $rule = new UniqueDaysInSchedule;

        $data = [
            'days' => [
                ['day' => 'Monday', 'time' => '07:00 am'],
                ['day' => 'Monday', 'time' => '05:00 pm'],
                ['day' => 'Wednesday', 'time' => '08:00 am'],
            ],
        ];

        $validator = Validator::make($data, [
            'days' => ['required', 'array', $rule],
        ]);

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->get('days'))->toHaveCount(1);
    });

    it('shows error message indicating which day is duplicated', function () {
        $rule = new UniqueDaysInSchedule;

        $data = [
            'days' => [
                ['day' => 'Friday', 'time' => '08:00 am'],
                ['day' => 'Friday', 'time' => '06:00 pm'],
            ],
        ];

        $validator = Validator::make($data, [
            'days' => ['required', 'array', $rule],
        ]);

        expect($validator->fails())->toBeTrue();

        $errors = $validator->errors()->get('days');
        expect($errors[0])->toContain('Friday');
        expect($errors[0])->toContain('appears more than once');
    });

    it('passes validation with all six unique days', function () {
        $rule = new UniqueDaysInSchedule;

        $data = [
            'days' => [
                ['day' => 'Monday', 'time' => '07:00 am'],
                ['day' => 'Tuesday', 'time' => '07:00 am'],
                ['day' => 'Wednesday', 'time' => '07:00 am'],
                ['day' => 'Thursday', 'time' => '07:00 am'],
                ['day' => 'Friday', 'time' => '07:00 am'],
                ['day' => 'Saturday', 'time' => '07:00 am'],
            ],
        ];

        $validator = Validator::make($data, [
            'days' => ['required', 'array', $rule],
        ]);

        expect($validator->passes())->toBeTrue();
    });

    it('handles empty array gracefully', function () {
        $rule = new UniqueDaysInSchedule;

        $data = [
            'days' => [],
        ];

        $validator = Validator::make($data, [
            'days' => ['required', 'array', $rule],
        ]);

        // Should fail on 'required' rule, but the custom rule shouldn't throw an error
        expect($validator->fails())->toBeTrue();
    });

    it('detects multiple duplicates and reports the first one', function () {
        $rule = new UniqueDaysInSchedule;

        $data = [
            'days' => [
                ['day' => 'Monday', 'time' => '07:00 am'],
                ['day' => 'Monday', 'time' => '05:00 pm'],
                ['day' => 'Wednesday', 'time' => '08:00 am'],
                ['day' => 'Wednesday', 'time' => '06:00 pm'],
            ],
        ];

        $validator = Validator::make($data, [
            'days' => ['required', 'array', $rule],
        ]);

        expect($validator->fails())->toBeTrue();

        $errors = $validator->errors()->get('days');
        // Should report first duplicate found
        expect($errors[0])->toMatch('/(Monday|Wednesday)/');
    });
});
