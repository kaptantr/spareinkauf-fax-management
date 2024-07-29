<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MusteriTanim extends Model
{
    protected $table = 'musteri_tanimlari';

    public $timestamps = false;

    protected $fillable = [
        'hitap_sekli',
        'baslik',
        'ad',
        'soyad',
        'konu_alani',
        'posta_kodu',
        'ulke',
        'il',
        'ilce',
        'adres',
        'siniflandirma',
        'derecelendirme',
        'degerlendirme_havuzu',
        'acik_mi',
        'mesai_baslangic',
        'mesai_bitis',
        'tel',
        'fax',
        'web_site',
        'teslimat_ulke',
        'teslimat_il',
        'teslimat_ilce',
        'teslimat_adres',
        'teslimat_telefon_no',
        'teslimat_fax_no',
        'teslimat_posta_kodu',
        'mail',
        'kara_liste',
        'fax_numara_guncellenmis',
        'fax_basarilimi',
    ];

    public function siparis()
    {
        return $this->belongsTo(Siparis::class);
    }

}
