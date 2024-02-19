<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    protected $primaryKey = 'loan_id';
    protected $keyType = 'string';
    protected $fillable = [
        'loan_id',
        'user_id',
        'loan_date',
        'restore_date',
        'status',
        'note',
        'restore_at',
        'penalty'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanDetails()
    {
        return $this->hasMany(LoanDetail::class, 'loan_id', 'loan_id');
    }

    // Deprecate // because I'm change database design simple
//    public function restore()
//    {
//        return $this->hasOne(Restore::class);
//    }
}
