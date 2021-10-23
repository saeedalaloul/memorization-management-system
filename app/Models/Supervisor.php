<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = ['id','grade_id'];

    public $timestamps = false;

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('user', function ($q) use ($val) {
                $q->where('name', 'LIKE', "%$val%");
            });
    }

    // علاقة بين المشرفين والمستخدمين لجلب اسم المشرف في جدول المشرفين
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    // علاقة بين المشرفين والمستخدمين لجلب اسم المشرف في جدول المشرفين
    public function grade()
    {
        return $this->belongsTo('App\Models\Grade', 'grade_id');
    }
}
