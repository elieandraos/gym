<?php

use App\Models\User;
use App\Rules\DoesNotOverlapWithOtherMemberBookings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    setupUsersAndBookings();

    $this->member = User::query()->members()->with('memberBookings')->first();
    $this->trainer = User::query()->trainers()->inRandomOrder()->first();

    $this->startDate = $this->member->memberBookings[0]->start_date;
    $this->endDate = $this->member->memberBookings[0]->end_date;

    $this->data = [
        'start_date' => Carbon::today(),
        'member_id' => $this->member->id,
        'trainer_id' => $this->trainer->id,
        'nb_sessions' => 12,
        'days' => [
            ['day' => 'Monday', 'time' => '07:00 am'],
            ['day' => 'Wednesday', 'time' => '07:00 am'],
        ],
    ];
});

function validate(array $data): bool
{
    $rule = new DoesNotOverlapWithOtherMemberBookings();
    $rule->setData($data);

    $validator = Validator::make($data, [
        'start_date' => [$rule],
    ]);

    return $validator->passes();
}

test('validator passes when start and end dates are completely after the member\'s booking', function () {
    $this->data['start_date'] = Carbon::today()->addMonths(2);
    $result = validate($this->data);
    expect($result)->toBeTrue();
});

test('validator passes when start and end dates are completely before the member\'s booking', function () {
    $this->data['start_date'] = Carbon::today()->subMonths(6);
    $result = validate($this->data);
    expect($result)->toBeTrue();
});

test('validator fails when start date is within the member\'s booking', function () {
    $this->data['start_date'] = Carbon::today();
    $result = validate($this->data);
    expect($result)->toBeFalse();
});

test('validator fails when end date is within the member\'s booking', function () {
    $this->data['start_date'] = Carbon::today()->subWeeks(3);
    $result = validate($this->data);
    expect($result)->toBeFalse();
});

test('validator passes if required data (member,days,nb sessions) are not filled', function () {
    $this->data['member_id'] = null;
    $this->data['days'] = [];

    $result = validate($this->data);
    expect($result)->toBeTrue();
});
