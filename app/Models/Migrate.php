<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Migrate extends Model
{

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'birth_date', 'phone', 'subscribe', 'suspend', 'email_verified_at'
    ];

    protected $hidden = [
	   'password',
	   'remember_token'
	];

	protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
