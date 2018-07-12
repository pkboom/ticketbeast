<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationEmail;

class Invitation extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFindByCode($query, $code)
    {
        return $query->where('code', $code)->firstOrFail();
    }

    public function hasBeenUsed()
    {
        return $this->user_id !== null;
    }

    public function send()
    {
        Mail::to($this->email)->send(new InvitationEmail($this));
    }
}
