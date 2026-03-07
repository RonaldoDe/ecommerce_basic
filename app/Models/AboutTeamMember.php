<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutTeamMember extends Model
{
    protected $table = 'about_team_members';

    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo',
        'linkedin',
        'twitter',
        'email',
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'order'  => 'integer',
    ];

    /**
     * Solo miembros activos ordenados
     */
    public function scopeVisible($query)
    {
        return $query->where('active', true)->orderBy('order');
    }
}