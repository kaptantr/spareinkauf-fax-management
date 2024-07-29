<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class SiparisDetayController extends Controller
{
    public function index(Content $content, $siparis_id=0)
    {

        return $content
            ->title('Sipariş Ekle&emsp;')
            ->header('Siparişler&nbsp;>&nbsp; Sipariş Ekle')
            ->description(' ')
            ->row(function (Row $row) use ($siparis_id) {
                $row->column(12, function (Column $column) use ($siparis_id) {
                    $column->append(Dashboard::stok_bilgileri($siparis_id));
                });
            });
    }
}
