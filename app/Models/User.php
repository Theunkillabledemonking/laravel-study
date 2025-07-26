<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Faker\Provider\bg_BG\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    // 일대다 연관관계 정의
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    // 일대다 역방향 관계
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 다수 연관관계
    public function phoneNumbers()
    {
        return $this->hasManyThrough(PhoneNumber::class, Contact::class);
    }
    // 단일 연관관계
    public function phoneNumber()
    {
        return $this->hasOneThrough(PhoneNumber::class, Contact::class);
    }
    // 다대다 연관관계
    public function contactsA()
    {
        return $this->belongsToMany(Contact::class);
    }
    // 다대다 역방향 연관관계
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    // 피벗테이블 필드 추가
    public function contactsB()
    {
        return $this->belongsToMany(Contact::class)
            ->withTimestamps()
            ->withPivot('status', 'preferred_greeting');
    }
}

$user = User::first();
$userContacts = $user->contacts;

$userName = $contact->user->name;