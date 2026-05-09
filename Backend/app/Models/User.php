<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // Relasi 1:1
    public function siswa()    { return $this->hasOne(Siswa::class); }
    public function guru()     { return $this->hasOne(Guru::class); }
    public function orangTua() { return $this->hasOne(OrangTua::class); }

    // Relasi 1:N
    public function notifikasis() { return $this->hasMany(Notifikasi::class); }

    // Helper cek role
    public function isAdmin()    { return $this->role === 'admin'; }
    public function isSiswa()    { return $this->role === 'siswa'; }
    public function isOrangTua() { return $this->role === 'orang_tua'; }
    public function isGuru()     { return $this->role === 'guru'; }
}