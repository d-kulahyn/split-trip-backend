<?php

namespace App\Models;

use App\Domain\Enum\DebtStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseDebt extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'expense_debts';

    /**
     * @var string[]
     */
    protected $fillable = [
        'expense_id',
        'amount',
        'currency',
        'to',
        'from',
        'status',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'status' => DebtStatusEnum::class,
            'amount' => 'float',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * @return BelongsTo
     */
    public function debtor(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'from');
    }

    /**
     * @return BelongsTo
     */
    public function creditor(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'to');
    }
}
