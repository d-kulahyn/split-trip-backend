<?php

namespace App\Models;

use App\Domain\Enum\DebtReminderPeriodEnum;
use Database\Factories\CustomerFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'need_to_change_password',
        'social_id',
        'social_type',
        'avatar',
        'social_type',
        'email_verified_at',
        'currency',
        'firebase_cloud_messaging_token',
        'push_notifications',
        'email_notifications',
        'debt_reminder_period',
        'avatar_color',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'    => 'datetime',
        'debt_reminder_period' => DebtReminderPeriodEnum::class,
    ];

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->getAttribute('password');
    }

    /**
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return (bool)$this->email_verified_at;
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'friends', 'customer_id', 'friend_id');
    }
}

