<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintBoxCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $timestamps = false;

    public function scopeSearch($query, $val)
    {
        return $query
            ->where('id', 'like', '%' . $val . '%')
            ->Orwhere('name', 'like', '%' . $val . '%');
    }
}
