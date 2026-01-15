# Release Notes

This release focuses on stability improvements and payment tracking. A major architectural refactor eliminates 502 errors caused by circular references in API resources, while a new payment amount field enables tracking booking costs directly in the system.

## 💰 Payment Amount Tracking

Bookings now include an `amount` field to track payment information.

**What's New:**
- Amount input on booking creation form
- Default value of 270.00 USD
- Payment status widget on member profiles displays booking amounts
- Amount included in booking API responses

This enables trainers and admins to track payments alongside session bookings without relying on external tools.

## 🛡️ API Resource Architecture Refactor

A significant architectural change addresses 502 Bad Gateway errors that occurred due to circular references in API resources.

**Problem:**
User models with `$appends` attributes caused infinite recursion when serialized through nested relationships (e.g., Booking → Member → Bookings → Member...).

**Solution:**
- Established strict serialization rules for API resources
- Resources serialize downward (parent → children) but never upward
- Controllers now pass data via separate props instead of nested relationships
- Calendar event serialization extracts values as plain strings before building response arrays

**Affected Areas:**
- `BookingResource`, `MemberResource`, `TrainerResource`
- Calendar controllers (daily and weekly views)
- Member and booking show pages

## 🔧 Bug Fixes

**Calendar 502 Errors:**
- Fixed booking slot serialization in daily and weekly calendar views
- Explicit relationship loading after flatMap operations ensures User models are complete for profile photo URL generation

**Member Booking History:**
- Fixed booking history page not displaying by passing bookings as a separate prop rather than through the member resource

## 🎨 UI Improvements

**Booking Slot Workouts:**
- Zero weight values now allowed for bodyweight exercises (bench dips, pull-ups, etc.)
- Previous sessions modal includes a session limit selector (1-5 sessions)
- Modal height locks during loading to prevent visual jumping

**Form Components:**
- TextInput component gains `fullWidth` prop (default true) for flexible width control
- Improved payment status layout in booking creation form

## 🔧 Technical Notes

**Database Changes:**
- Added `amount` decimal column to `bookings` table (precision 10, scale 2)

**Model Updates:**
- `Booking` model includes `amount` in fillable array
- Booking factory generates random amounts for testing
- Booking validation accepts nullable numeric amount values

**CLAUDE.md Guidelines:**
- Documented API resource serialization rules to prevent future circular reference issues
- Added explicit relationship map showing allowed serialization directions

## Summary

This release delivers critical stability fixes for the calendar and member pages while introducing payment tracking capabilities. The API resource refactor establishes architectural patterns that prevent circular reference issues going forward. All changes maintain the existing user experience while improving system reliability.