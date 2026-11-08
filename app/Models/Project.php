<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Hidden(['api_key_secure'])]
class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'judul',
        'deskripsi',
        'tema_agent',
        'api_key_secure',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'api_key_secure' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ProjectActivityLog::class);
    }
}
