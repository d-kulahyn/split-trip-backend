<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLogCustomer extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'activity_log_customer';

    /**
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'activity_log',
        'status',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
    }
}
