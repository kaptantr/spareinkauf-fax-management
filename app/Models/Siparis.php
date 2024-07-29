<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siparis extends Model
{
    protected $table = 'siparisler';

    public $timestamps = false;

    protected $fillable = [
        'musteri_id',
        'siparis_islendimi',
        'tarih',
        'pdf_adi',
        'listelensin',
        'response_json',
    ];

    public function stoks()
    {
        return $this->hasMany(StokTanim::class, 'siparis_id', 'id');
    }

    public function musteri()
    {
        return $this->hasOne(MusteriTanim::class, 'id', 'musteri_id');
    }

}
