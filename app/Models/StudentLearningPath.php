<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLearningPath extends Model
{
    protected $fillable = ['user_id', 'path_type', 'unlocked_level'];

    public const PATH_NO_CODING  = 'no_coding';
    public const PATH_LOW_SCORE  = 'low_score';
    public const PATH_HIGH_SCORE = 'high_score';

    public const LEVEL_INTRODUCTION = 'Introduction';
    public const LEVEL_BEGINNER     = 'Beginner';
    public const LEVEL_INTERMEDIATE = 'Intermediate';
    public const LEVEL_ADVANCED     = 'Advanced';

    public const LEVEL_RANKS = [
        'Introduction' => 1,
        'Beginner'     => 2,
        'Intermediate' => 3,
        'Advanced'     => 4,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUnlockedLevelRankAttribute(): int
    {
        return self::LEVEL_RANKS[$this->unlocked_level] ?? 1;
    }

    public function canAccessLevel(string $level): bool
    {
        $rank = self::LEVEL_RANKS[$level] ?? 99;
        return $rank <= $this->unlocked_level_rank;
    }

    public function getPathLabelAttribute(): string
    {
        return match ($this->path_type) {
            self::PATH_NO_CODING  => 'Introduction Path',
            self::PATH_LOW_SCORE  => 'Progressive Path',
            self::PATH_HIGH_SCORE => 'Advanced Track',
            default               => 'Learning Path',
        };
    }

    public function getNextLevelAttribute(): ?string
    {
        $current = self::LEVEL_RANKS[$this->unlocked_level] ?? 1;
        foreach (self::LEVEL_RANKS as $level => $rank) {
            if ($rank === $current + 1) return $level;
        }
        return null;
    }
}
