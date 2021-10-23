<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Father extends Model
{
    protected $fillable = ['id'];
    public $timestamps = false;

    // علاقة بين ولي أمر الطالب وأبنائهم لجلب اسم الطالب في جدول ولي أمر الطالب
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function user(){
        return $this->belongsTo('App\Models\User','id');
    }
}
