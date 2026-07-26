<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingInvitation extends Model
{
    protected $fillable = ['email', 'invitable_id', 'invitable_type'];

    public function invitable()
    {
        return $this->morphTo();
    }
}
