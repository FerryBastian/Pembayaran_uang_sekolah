<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'kelas_id', 'orang_tua_id',
        'nisn', 'nama', 'alamat', 'jenis_kelamin', 'tanggal_lahir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function kelas()    { return $this->belongsTo(Kelas::class); }
    public function orangTua() { return $this->belongsTo(OrangTua::class); }

    public function tagihanSiswas()
    {
        return $this->hasMany(TagihanSiswa::class);
    }
}