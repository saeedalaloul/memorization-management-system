<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use HasRoles;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_role',
        'dob',
        'phone',
        'profile_photo',
        'identification_number',
        'email_verified_at',
        'last_seen',
        'status',
    ];

    const ADMIN_ROLE = "أمير المركز";
    const SUPERVISOR_ROLE = "مشرف";
    const EXAMS_SUPERVISOR_ROLE = "مشرف الإختبارات";
    const ACTIVITIES_SUPERVISOR_ROLE = "مشرف الأنشطة";
    const OVERSIGHT_SUPERVISOR_ROLE = "مشرف الرقابة";
    const COURSES_SUPERVISOR_ROLE = "مشرف الدورات";
    const TEACHER_ROLE = "محفظ";
    const TESTER_ROLE = "مختبر";
    const ACTIVITY_MEMBER_ROLE = "منشط";
    const OVERSIGHT_MEMBER_ROLE = "مراقب";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo && Storage::disk('users_images')->exists($this->profile_photo)) {
            return Storage::disk('users_images')->url($this->profile_photo);
        }

        return asset('assets/images/teacher.png');
    }

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->orWhere('name', 'like', '%' . $val . '%')
            ->orWhere('identification_number', 'like', '%' . $val . '%')
            ->orWhere('email', 'like', '%' . $val . '%')
            ->orWhere('phone', 'like', '%' . $val . '%');
    }

    public function user_info()
    {
        return $this->hasOne('App\Models\UserInfo', 'id');
    }

    public function user_fcm_token()
    {
        return $this->hasOne('App\Models\UserFcmToken', 'id');
    }

    public function tester()
    {
        return $this->hasOne('App\Models\Tester', 'id');
    }

    public function supervisor()
    {
        return $this->hasOne('App\Models\Supervisor', 'id');
    }

    public function father()
    {
        return $this->hasOne('App\Models\Father', 'id');
    }

    public function student()
    {
        return $this->hasOne('App\Models\Student', 'id');
    }

    public function oversight_member()
    {
        return $this->hasOne('App\Models\OversightMember', 'id');
    }

    public function activity_member()
    {
        return $this->hasOne('App\Models\ActivityMember', 'id');
    }
}
