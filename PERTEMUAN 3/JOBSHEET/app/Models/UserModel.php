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
    protected $fillable = ['username','password','nama','level_id','created_at','updated_at','foto'];
    protected $hidden=['password']; //jangan ditampilkan saat select
    protected $casts = ['password' => 'hashed']; //casting password agar otomatis di hash

    // relasi ke tabel level
    public function level():BelongsTo{
        // return $this->hasOne(LevelModel::class);
        return $this->belongsTo(LevelModel::class,'level_id','level_id');
    }

    // mendapatkan nama role
    public function getRoleName():string{
        return $this->level->level_nama;
    }

    // cek apakah user memiliki role tertentu
    public function hasRole( $role):bool{
        return $this->level->level_kode == $role;
    }

    // mendapatkan koode role
    public function getRole(){
        return $this->level->level_kode;
    }
    

}
