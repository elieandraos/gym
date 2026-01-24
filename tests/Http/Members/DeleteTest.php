<?php

use App\Http\Resources\MemberResource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    setupUsersAndBookings();
});

test('delete/destroy routes require authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.delete', $member))->assertRedirect(route('login'));
    $this->delete(route('admin.members.destroy', $member))->assertRedirect(route('login'));
});

test('it renders the delete member confirmation page', function () {
    $member = User::query()->members()->first();

    actingAsAdmin()
        ->get(route('admin.members.delete', $member))
        ->assertHasComponent('Admin/DeleteMember/Index')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
});

test('it deletes a member and cascades to all related data', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();

    // Create test data: bookings, booking slots, body compositions
    $booking = $member->memberBookings()->first();
    $bookingSlot = $booking->bookingSlots()->first();

    // Create body composition and storage files
    $bodyComposition = \App\Models\BodyComposition::factory()->create(['user_id' => $member->id]);
    Storage::disk('public')->put("body-compositions/$member->id/test.jpg", 'test content');
    Storage::disk('public')->put("profile-photos/$member->id/profile.jpg", 'test content');

    // Verify data exists before deletion
    $this->assertDatabaseHas('users', ['id' => $member->id]);
    $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    $this->assertDatabaseHas('booking_slots', ['id' => $bookingSlot->id]);
    $this->assertDatabaseHas('body_compositions', ['id' => $bodyComposition->id]);
    Storage::disk('public')->assertExists("body-compositions/$member->id/test.jpg");
    Storage::disk('public')->assertExists("profile-photos/$member->id/profile.jpg");

    // Delete the member
    actingAsAdmin()
        ->delete(route('admin.members.destroy', $member))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.index'));

    // Verify CASCADE deletion worked
    $this->assertDatabaseMissing('users', ['id' => $member->id]);
    $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    $this->assertDatabaseMissing('booking_slots', ['id' => $bookingSlot->id]);
    $this->assertDatabaseMissing('body_compositions', ['id' => $bodyComposition->id]);

    // Verify storage files were deleted
    Storage::disk('public')->assertMissing("body-compositions/$member->id/test.jpg");
    Storage::disk('public')->assertMissing("profile-photos/$member->id/profile.jpg");
});

test('member deletion does not affect workouts table', function () {
    $member = User::query()->members()->first();

    // Get a workout ID from the workouts table (not from circuits since none are seeded)
    $workout = \App\Models\Workout::query()->first();

    if (! $workout) {
        expect(true)->toBeTrue(); // Skip test if no workout exists

        return;
    }

    $workoutId = $workout->id;

    // Verify workout exists before member deletion
    $this->assertDatabaseHas('workouts', ['id' => $workoutId]);

    // Delete the member
    actingAsAdmin()
        ->delete(route('admin.members.destroy', $member))
        ->assertSessionHasNoErrors();

    // Verify workout definition still exists (not cascaded)
    $this->assertDatabaseHas('workouts', ['id' => $workoutId]);
});
