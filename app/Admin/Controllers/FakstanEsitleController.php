<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakstanEsitleController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Siparişler&emsp;')
            ->header('Siparişler&nbsp;&nbsp;>&nbsp;Gelen Faksları Al')
            ->description(' ')
            ->row(function (Row $row) {

                $row->column(12, function (Column $column) {
                    $column->append(Dashboard::fakstan_esitle());
                });
            });
    }

}
