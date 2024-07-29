<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokTanim extends Model
{
    public $timestamps = false;

    protected $table = 'stok_tanimlari';

    protected $fillable = [
        'siparis_id',
        'kart_id',
        'adet',
        'magento_id',
        'shopware_id',
        'shopify_id',
        'woocommerce_id',
    ];

    public function siparis()
    {
        return $this->belongsTo(Siparis::class);
    }

    public function kart()
    {
        return $this->hasOne(MagazaStok::class, 'id', 'kart_id');
    }

}
