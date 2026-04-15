<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'loan_date', 'due_date', 'return_date', 
        'additional_fine', 'fine_reason', 'is_paid', 'payment_method', 'payment_proof'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function book(): BelongsTo { return $this->belongsTo(Book::class); }

    public function getTotalDendaAttribute()
    {
        $tarifDenda = Setting::where('key', 'fine_per_day')->first()->value ?? 0;
        $deadline = Carbon::parse($this->due_date);
        $sekarang = $this->return_date ? Carbon::parse($this->return_date) : now();
        $diff = (int) $deadline->diffInDays($sekarang, false);
        $hariTelat = $diff > 0 ? $diff : 0;

        return ($hariTelat * $tarifDenda) + ($this->additional_fine ?? 0);
    }

    public function getHariTelatAttribute()
    {
        $deadline = Carbon::parse($this->due_date);
        $sekarang = $this->return_date ? Carbon::parse($this->return_date) : now();
        $diff = (int) $deadline->diffInDays($sekarang, false);
        return $diff > 0 ? $diff : 0;
    }
}