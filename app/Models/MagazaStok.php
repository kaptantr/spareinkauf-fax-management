<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagazaStok extends Model
{
    public $timestamps = false;

    protected $table = 'magaza_stoklari';

    protected $fillable = [
        'woocommerce_id',
        'stok_karti',
        'urun_adi',
        'urun_link',
        'stok_durumu',
        'urun_sku',
        'adet',
        'fiyat',
        'magaza_adresi',
        'esitleme_tarihi',
    ];

}
