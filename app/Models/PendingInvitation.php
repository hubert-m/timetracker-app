<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingInvitation extends Model
{
    protected $fillable = ['email', 'invitable_id', 'invitable_type', 'inviter_id'];

    public function invitable()
    {
        return $this->morphTo();
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
