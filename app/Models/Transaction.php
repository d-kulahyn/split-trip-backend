<?php

namespace App\Models;

use App\Domain\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'transactions';

    protected $casts = [
        'status' => StatusEnum::class,
        'amount' => 'float',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'amount',
        'from',
        'to',
        'currency',
        'status',
        'group_id',
        'rate',
        'base_currency',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function fromC(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'from');
    }

    public function toC(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'to');
    }
}
