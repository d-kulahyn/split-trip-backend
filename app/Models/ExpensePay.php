<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpensePay extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'expense_pays';

    /**
     * @var string[]
     */
    protected $fillable = [
        'expense_id',
        'amount',
        'currency',
        'payer_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'payer_id');
    }
}
