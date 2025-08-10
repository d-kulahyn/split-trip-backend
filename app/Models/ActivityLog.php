<?php

namespace App\Models;

use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'activity_logs';

    protected $casts = [
        'details'     => 'array',
        'action_type' => ActivityLogActionTypeEnum::class,
        'status'      => StatusEnum::class,
        'created_at'  => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'group_id',
        'action_type',
        'details',
        'status',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
