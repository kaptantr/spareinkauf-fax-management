<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class SiparisEkleController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Sipariş Ekle&emsp;')
            ->header('Siparişler&nbsp;&nbsp;>&nbsp; Sipariş Ekle')
            ->description(' ')
            ->row(function (Row $row) {

                $row->column(3, function (Column $column) {
                    $column->append(Dashboard::siparisler());
                });

                $row->column(9, function (Column $column) {
                    $column->append(Dashboard::siparis_bilgileri());
                });
            });
    }
}
