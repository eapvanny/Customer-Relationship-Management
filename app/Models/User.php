<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory,HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'family_name',
        'name',
        'family_name_latin',
        'name_latin',
        'email',
        'password',
        'username',
        'staff_id_card',
        'phone_no',
        'photo',
        'gender',
        'user_lang',
        'role_id',
        'position',
        'status',
        'area',
        'type',
        'manager_id',
        'rsm_id',
        'asm_id',
        'created_by',
        'deleted_by'
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
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function report()
    {
        return $this->hasMany(Report::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Optional: Relationship to get users managed by this user
    public function managedUsers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function getFullNameAttribute()
    {
        return $this->family_name . ' ' . $this->name;
    }
    public function getFullNameLatinAttribute()
    {
        return $this->family_name_latin . ' ' . $this->name_latin;
    }
    // public function getPhotoUrlAttribute()
    // {
    //     return $this->photo ? asset('storage/' . $this->photo) : asset('images/avatar.png'); // Default photo if none exists
    // }
}
