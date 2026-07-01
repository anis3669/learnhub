<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseUnlock extends Model
{
    protected $fillable = ['prerequisite_level', 'unlocks_level'];
}
