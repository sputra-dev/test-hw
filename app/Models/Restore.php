<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restore extends Model
{
    use HasFactory;

    protected $table = 'restores';

    protected $fillable = [
        'loan_id',
        'restore_date',
        'status',
        'penalty'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
