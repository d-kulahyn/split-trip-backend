<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debtor extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'expense_debtors';

    /**
     * @var string[]
     */
    protected $fillable = [
        'expense_id',
        'amount',
        'currency',
        'debtor_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'debtor_id');
    }
}
