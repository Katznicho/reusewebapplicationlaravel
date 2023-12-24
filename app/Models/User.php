<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account_balance',
        'account_currency',
        'role',
        'is_admin',
        'is_wallet_active',
        'show_wallet_balance',
        'pin',
        'is_user_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    //user has many donations
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    //user has many payments
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    //user has many deliveries
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    //user has many user notifications
    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }
}
