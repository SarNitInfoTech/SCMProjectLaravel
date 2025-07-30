<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin'; // for custom auth guard

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'phone',
        'profile_photo',
        'is_active',
        'two_factor_enabled',
        'two_factor_secret',
        'email_verified_at',
        'last_login_at'
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];
}
