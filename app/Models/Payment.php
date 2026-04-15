<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['loan_id', 'amount_paid', 'payment_method', 'proof_file', 'status'];

    public function loan() {
        return $this->belongsTo(Loan::class);
    }

    protected static function booted()
    {
        static::updated(function ($payment) {
            if ($payment->status === 'success') {
                $payment->loan->update(['total_fine' => 0]);
            }
        });
    }
}