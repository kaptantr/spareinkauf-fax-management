<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaxGonder extends Model
{
    protected $table = 'fax_ayarlari';

    public $timestamps = false;

    protected $fillable = [
        'gidecek_fax',
        'gidecek_pdf_url',
    ];

}
