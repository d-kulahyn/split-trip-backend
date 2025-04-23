<?php

namespace App\Models;

use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'expenses';

    /**
     * @var string[]
     */
    protected $fillable = [
        'description',
        'category',
        'group_id',
        'created_at',
        'final_currency',
    ];

    /**
     * @return BelongsTo
     */
    public function members(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function payers(): HasMany
    {
        return $this->hasMany(Payer::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(ExpenseDebt::class);
    }

    public function debtors(): HasMany
    {
        return $this->hasMany(Debtor::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * @return ExpenseFactory
     */
    protected static function newFactory(): ExpenseFactory
    {
        return ExpenseFactory::new();
    }
}
