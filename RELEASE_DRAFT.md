# Release Notes

This release focuses on test infrastructure and developer experience. A complete restructuring of the test suite introduces domain-based organization, making tests easier to locate and maintain. New testing documentation establishes patterns for the team to follow going forward.

## 🧪 Test Suite Restructuring

The test suite has been reorganized from a flat structure to a domain-based hierarchy that mirrors the application's feature structure.

**New Directory Structure:**
```
tests/
├── Http/                              # Feature tests organized by domain
│   ├── Bookings/
│   │   ├── IndexTest.php
│   │   ├── CreateTest.php
│   │   ├── Freeze/
│   │   ├── Unfreeze/
│   │   └── MarkAsPaid/
│   ├── BookingSlots/
│   │   └── Circuits/
│   │       └── Workouts/
│   ├── Members/
│   │   ├── PersonalInfo/
│   │   ├── BodyComposition/
│   │   └── BookingHistory/
│   ├── Trainers/
│   ├── Workouts/
│   ├── Calendar/
│   └── Settings/
├── Console/                           # Artisan command tests
├── Notifications/                     # Email/SMS/Push tests
└── Unit/                              # Pure unit tests (minimal)
```

**Key Principles:**
- Tests organized by domain (what users interact with), not code structure
- One file per user-facing action: `IndexTest.php`, `CreateTest.php`, `ShowTest.php`, `UpdateTest.php`, `DeleteTest.php`
- Sub-features with their own controllers get subfolders (e.g., `Bookings/Freeze/`)
- Test file location now predictable based on controller structure

## 📚 Testing Documentation

A new comprehensive testing guide has been added at `learnings/TESTING.md`.

**Documentation Includes:**
- Core testing philosophy (feature tests over unit tests, test through HTTP)
- Directory structure and naming conventions
- Test organization rules with concrete examples
- Data setup guidelines using Pest.php helpers
- Custom Inertia assertion macros (`assertHasComponent`, `assertHasProp`, `assertHasResource`)
- Common pitfalls to avoid

## 🔧 Bug Fixes

**Booking Slot Circuit Operations:**
- Fixed delete operation for booking slot circuits
- Fixed update operation for booking slot circuits

**Validation Messages:**
- Fixed unique days validation error message formatting

## 🔧 Technical Notes

**Test Code Improvements:**
- Refactored tests to use type hints for factory-created models
- Chained `expect()` assertions using `->and()` for cleaner test code
- PHPDoc annotations added to suppress false-positive IDE warnings

**phpunit.xml Configuration:**
Four test suites configured: `Http`, `Console`, `Unit`, `Notifications`


## Summary

This release establishes a scalable foundation for the test suite. The domain-based organization makes it straightforward to find tests related to any feature, and the new documentation ensures consistent patterns across the codebase. These improvements in developer experience pay dividends as the application grows.
