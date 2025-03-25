<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable
{
    use HasFactory;

    protected $table='m_user';
    protected $primaryKey='user_id';
    protected $fillable = ['username','password','nama','level_id','created_at','updated_at'];
    protected $hidden=['password']; //jangan ditampilkan saat select
    protected $casts = ['password' => 'hashed']; //casting password agar otomatis di hash

    public function level():BelongsTo{
        // return $this->hasOne(LevelModel::class);
        return $this->belongsTo(LevelModel::class,'level_id','level_id');
    }
    
}
