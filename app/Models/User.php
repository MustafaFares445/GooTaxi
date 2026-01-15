<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Traits\FilterQueries\UserFilterQuery;
use App\Traits\HasMediaConversions;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property string|null $phone_number
 * @property bool $is_admin
 * @property-read CarbonInterface|null $email_verified_at
 * @property string|null $email_verification_otp
 * @property CarbonInterface|null $email_verification_otp_expires_at
 * @property string|null $password_reset_otp
 * @property CarbonInterface|null $password_reset_otp_expires_at
 * @property-read string $password
 * @property-read string|null $remember_token
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasMediaConversions, Notifiable, UserFilterQuery;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'is_admin',
        'email_verified_at',
        'email_verification_otp',
        'email_verification_otp_expires_at',
        'password_reset_otp',
        'password_reset_otp_expires_at',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'phone_number' => 'string',
            'is_admin' => 'boolean',
            'email_verified_at' => 'datetime',
            'email_verification_otp' => 'string',
            'email_verification_otp_expires_at' => 'datetime',
            'password_reset_otp' => 'string',
            'password_reset_otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'remember_token' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token = null): void
    {
        $this->notify(new ResetPasswordNotification);
    }
}
