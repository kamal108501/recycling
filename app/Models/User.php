<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\IdEncoder;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users_master';
    protected $primaryKey = 'userid';
    protected $appends = ['encoded_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'usermobile',
        'app_last_login_at',
        'profile_img',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope('active_not_deleted', function (Builder $builder) {
            $builder->where('is_active', 1)
                ->whereNull('deleted_at');
        });
    }

    public function scopeActive($query)
    {
        return $query->withoutGlobalScope('active_not_deleted')
            ->where('is_active', 1)
            ->whereNull('deleted_at');
    }

    public function getEncodedIdAttribute()
    {
        return encode($this->userid);
    }
}
