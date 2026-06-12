<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean'
    ];

    /**
     * Проверить, истек ли срок действия кода
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Проверить, действителен ли код
     */
    public function isValid()
    {
        return !$this->verified && !$this->isExpired();
    }
}
