<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id',
        'value',
        'type',
    ];

    /**
     * Answer relationship
     */
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * Conditionable relationship
     */
    public function conditionable()
    {
        return $this->morphTo();
    }
}
