<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tuas';

    protected $fillable = [
        'user_id', 'nama', 'no_hp', 'no_wa', 'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}