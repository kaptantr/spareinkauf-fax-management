<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaxKayit extends Model
{
    protected $table = 'fax_kayitlari';

    public $timestamps = false;

    protected $fillable = [
        'gonderen_faks',
        'pdf_adi',
        'tarih',
        'esitleme_tarihi',
    ];

}
