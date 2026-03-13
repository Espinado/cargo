<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function driver()
{
    return $this->hasOne(Driver::class);
}
public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
public function isAdmin(): bool
{
    return ($this->role ?? null) === 'admin';
}

    /**
     * ID компаний, чьи машины пользователь видит на карте и в Notikumi.
     * Админ — компании из mapon.keys; если пусто — все компании (чтобы события не были пустыми).
     * Менеджеры — только своя компания.
     *
     * @return array<int>
     */
    public function allowedMapCompanyIds(): array
    {
        if ($this->isAdmin()) {
            $keys = config('mapon.keys', []);
            $ids = array_values(array_map('intval', array_keys(array_filter($keys))));
            if ($ids !== []) {
                return $ids;
            }
            // Fallback: если ключи не настроены — показываем события по всем компаниям
            return \App\Models\Company::query()->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        }
        $id = $this->company_id ?? null;
        return $id !== null ? [(int) $id] : [];
    }
}
