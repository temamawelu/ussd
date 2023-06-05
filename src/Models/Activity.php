<?php

namespace Stilinski\Ussd\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($col){
            $col->uuid = Str::orderedUuid()->toString();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $guarded = ['id'];

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'session_id', 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'msisdn', 'msisdn');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id', 'session_id');
    }
}