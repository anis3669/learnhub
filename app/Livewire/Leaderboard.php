<?php

namespace App\Livewire;

use App\Models\Quiz;
use Livewire\Component;

class Leaderboard extends Component
{
    public $courseId = null;

    // Quick Sort Algorithm
    private function quickSort($array)
    {
        if (count($array) < 2) {
            return $array;
        }

        $pivot = $array[0];
        $left = [];
        $right = [];

        for ($i = 1; $i < count($array); $i++) {
            if ($array[$i]['score'] > $pivot['score']) {
                $left[] = $array[$i];
            } else {
                $right[] = $array[$i];
            }
        }

        return array_merge(
            $this->quickSort($left),
            [$pivot],
            $this->quickSort($right)
        );
    }

    public function getLeaderboardProperty()
    {
        $query = \App\Models\User::query()
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->withCount(['quizAttempts as total_score' => function ($query) {
                $query->select(\DB::raw('sum(score)'));
            }])
            ->orderBy('total_score', 'desc');

        if ($this->courseId) {
            $query->whereHas('quizAttempts', fn($q) => $q->where('course_id', $this->courseId));
        }

        $students = $query->get(['id', 'name', 'total_score']);

        // Convert to array for Quick Sort
        $data = $students->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'score' => $s->total_score ?? 0
        ])->toArray();

        // Apply Quick Sort
        return $this->quickSort($data);
    }

    public function render()
    {
        return view('livewire.leaderboard', [
            'leaderboard' => $this->leaderboard
        ]);
    }
}
