<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersPhoneDetails extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'userid',
        'app_version',
        'phone_os_version',
        'phone_uuid',
        'user_access_token',
        'device_token',
        'imei_no',
        'mobile_type',
        'created_at'
    ];
}
