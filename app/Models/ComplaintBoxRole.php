<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintBoxRole extends Model
{
    use HasFactory;

    protected $fillable = ['id'];
    public $timestamps = false;

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->OrwhereHas('role', function ($q) use ($val) {
                $q->where('name', 'like', "%$val%");
            });
    }

    // علاقة بين الأدوار وأدوار صندوق الشكاوي والإقتراحات لجلب اسم الدور في جدول أدوار صندوق الشكاوي والإقتراحات

    public function role()
    {
        return $this->belongsTo('Spatie\Permission\Models\Role', 'id');
    }
}
