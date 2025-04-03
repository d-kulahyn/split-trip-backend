<?php

namespace App\Models;

use App\Domain\Enum\ActivityLogActionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'activity_logs';

    protected $casts = [
        'details' => 'array',
        'action_type' => ActivityLogActionTypeEnum::class
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'group_id',
        'action_type',
        'details'
    ];
}
