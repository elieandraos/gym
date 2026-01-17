<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fillable attributes
 *
 * @property int $user_id
 * @property string $photo_path
 * @property Carbon $taken_at
 *
 * Auto-generated
 * @property-read int $id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 *
 * Relationships
 * @property-read User $user
 */
class BodyComposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo_path',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
