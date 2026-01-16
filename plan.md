# PhpStorm Annotation & Warning Fix Patterns

## Purpose
Reference document for building a `/annotate` custom command to automate PHPDoc annotations and warning fixes.

---

## 1. Model Annotations

### Pattern
```php
/**
 * Fillable attributes
 *
 * @property Type $attribute_name
 *
 * Auto-generated
 *
 * @property-read int $id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 *
 * Relationships
 *
 * @property-read RelatedModel|null $belongsToRelation
 * @property-read Collection<RelatedModel> $hasManyRelation
 *
 * Scopes
 *
 * @method static Builder|ModelClass scopeName()
 * @method static Builder|ModelClass scopeWithParams(Type $param)
 *
 * @mixin TraitName
 */
```

### Rules
| Element | Annotation | Example |
|---------|------------|---------|
| Fillable columns | `@property` | `@property string $name` |
| Nullable fillable | `@property` | `@property string\|null $phone` |
| id | `@property-read` | `@property-read int $id` |
| Timestamps | `@property-read` | `@property-read Carbon\|null $created_at` |
| Accessors | `@property-read` | `@property-read string $full_name` |
| BelongsTo | `@property-read` | `@property-read User $member` |
| HasOne | `@property-read` | `@property-read Profile\|null $profile` |
| HasMany | `@property-read` | `@property-read Collection<Post> $posts` |
| Scopes (no params) | `@method static` | `@method static Builder\|User active()` |
| Scopes (with params) | `@method static` | `@method static Builder\|User byRole(string $role)` |
| Traits | `@mixin` | `@mixin HasSettings` |

### Type Mappings from $casts
| Cast | PHPDoc Type |
|------|-------------|
| `'date'` | `Carbon` |
| `'datetime'` | `Carbon` |
| `'boolean'` | `bool` |
| `'integer'` | `int` |
| `'decimal:2'` | `string` |
| `'array'` | `array` |
| `EnumClass::class` | `EnumClass` |

---

## 2. Controller Annotations

### Unused Route Model Binding Parameters
```php
/** @noinspection PhpUnusedParameterInspection */
public function store(Request $request, ParentModel $parent, ChildModel $child): RedirectResponse
```
**When:** Parameter needed for route but not used in method body.

### Typed Variables for Generic Returns
```php
/** @var User $user */
$user = auth()->user();

/** @var User $user */
$user = request()->user();

/** @var ModelClass $model */
$model = $relationship->create([...]);

/** @var ModelClass|null $item */
$item = $collection->first();
```
**When:** Method returns generic type but you need specific type for IDE.

### Scope Calls with Query Builder
```php
/** @var Builder|Booking $query */
$query = Booking::query();
$results = $query->scopeName()->get();
```
**When:** Calling scope methods on query builder.

### Query Builder Method Calls
```php
// Use query() to start builder explicitly
$items = Model::query()->orderBy('name')->get();

// NOT: Model::orderBy('name')->get()
```
**When:** Calling builder methods directly on model class.

---

## 3. Model Internal Annotations

### Scope Calling Another Scope
```php
/** @noinspection PhpUndefinedMethodInspection - Scope calling another scope, PhpStorm can't recognize it */
#[AsScope]
public function forCalendar(Builder $query, Carbon $start, Carbon $end): Builder
{
    return $query->with([...])->anotherScope($start, $end);
}
```
**When:** A scope method calls another scope from same model.

### Relationship Calling Related Model's Scope
```php
/** @noinspection PhpUndefinedMethodInspection - Relationship calling Model scope */
public function activeItems(): HasMany
{
    return $this->hasMany(Item::class)->active();
}
```
**When:** Relationship definition calls a scope from the related model.

---

## 4. API Resource Annotations

### Pattern
```php
use App\Models\ModelClass;

/**
 * @mixin ModelClass
 */
class ModelResource extends JsonResource
```
**When:** Always add to resources to enable property autocomplete.

---

## 5. String Interpolation

### Avoid Unnecessary Curly Braces
```php
// Good - simple property access
"path/$user->id/file"

// Bad - unnecessary braces
"path/{$user->id}/file"
```
**When:** Simple property or variable access in strings.

---

## 6. Import Rules

### Always Import, Never Inline
```php
// Good
use App\Enums\Status;
$status = Status::Active;

// Bad
$status = \App\Enums\Status::Active;
```
**When:** Using any class reference.

---

## Command Implementation Notes

### `/annotate Model`
1. Read model file
2. Extract from `$fillable` array → generate `@property`
3. Extract from `$casts` array → determine types
4. Find relationship methods (return BelongsTo/HasOne/HasMany) → generate `@property-read`
5. Find scope methods (#[AsScope] attribute) → generate `@method static`
6. Find `use Trait` statements → generate `@mixin`
7. Add standard: `id`, `created_at`, `updated_at` as `@property-read`
8. Check for scope-calling-scope patterns → add `@noinspection`
9. Check for relationship-calling-scope patterns → add `@noinspection`

### `/annotate Controller`
1. Read controller file
2. Find route model binding params not used in body → add `@noinspection PhpUnusedParameterInspection`
3. Find `auth()->user()` / `request()->user()` calls → add `@var User`
4. Find `->create()` on relationships → add `@var ModelClass`
5. Find scope calls on `Model::query()` → add typed variable pattern
6. Check for inline qualified names → suggest imports

### `/annotate Resource`
1. Read resource file
2. Determine model from class name or usage
3. Add `@mixin ModelClass` to class PHPDoc
4. Add model import if missing

---

## Files Reference

### Models annotated this session:
- `app/Models/User.php` ✅
- `app/Models/Booking.php` ✅

### Controllers fixed this session:
- `app/Http/Controllers/Admin/BookingSlotCircuitWorkoutsController.php`
- `app/Http/Controllers/Admin/BookingSlotsController.php`
- `app/Http/Controllers/Admin/MembersController.php`
- `app/Http/Controllers/Admin/UserSettingsController.php`
- `app/Http/Controllers/Admin/WeeklyCalendarController.php`
- `app/Http/Controllers/Admin/DailyCalendarController.php`

### Resources annotated this session:
- `app/Http/Resources/BookingResource.php` ✅

### Pending (need annotation):
- All other models in `app/Models/`
- All other resources in `app/Http/Resources/`
- All other controllers in `app/Http/Controllers/`
