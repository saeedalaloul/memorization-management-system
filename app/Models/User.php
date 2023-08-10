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
        'gender',
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

    public const ADMIN_ROLE = "أمير المركز";
    public const SUPERVISOR_ROLE = "مشرف";
    public const SPONSORSHIP_SUPERVISORS_ROLE = "مشرف حلقات مكفولة";
    public const EXAMS_SUPERVISOR_ROLE = "مشرف الإختبارات";
    public const ACTIVITIES_SUPERVISOR_ROLE = "مشرف الأنشطة";
    public const OVERSIGHT_SUPERVISOR_ROLE = "مشرف الرقابة";
    public const COURSES_SUPERVISOR_ROLE = "مشرف الدورات";
    public const TEACHER_ROLE = "محفظ";
    public const TESTER_ROLE = "مختبر";
    public const ACTIVITY_MEMBER_ROLE = "منشط";
    public const OVERSIGHT_MEMBER_ROLE = "مراقب";
    public const STUDENT_ROLE = "طالب";
    public const FATHER_ROLE = "ولي أمر الطالب";

    public const MALE_GENDER = "male";
    public const FEMALE_GENDER = "female";

    public static function genders()
    {
        return [
            self::MALE_GENDER => 'ذكر',
            self::FEMALE_GENDER => 'أنثى',
        ];
    }

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

    public function sponsorships()
    {
        return $this->belongsToMany(Sponsorship::class, 'sponsorship_supervisors','sponsorship_supervisor_id');
    }
}
