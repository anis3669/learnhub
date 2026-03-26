<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Leaderboard extends Component
{
    public function getLeaderboardProperty()
    {
        $students = User::role('student')
            ->select('id', 'name')
            ->get();

        $data = $students->map(function ($student) {
            return [
                'id'    => $student->id,
                'name'  => $student->name,
                'score' => rand(600, 1000),   // temporary score for testing
            ];
        })->sortByDesc('score')->values()->toArray();

        return $data;
    }

    public function render()
    {
        return view('livewire.leaderboard', [
            'leaderboard' => $this->leaderboard   // ← This is the important line
        ]);
    }
}