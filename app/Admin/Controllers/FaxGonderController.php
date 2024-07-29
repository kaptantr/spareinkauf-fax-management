<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaxGonderController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Siparişler&emsp;')
            ->header('Siparişler&nbsp;&nbsp;>&nbsp;Fax Gönder')
            ->description(' ')
            ->row(function (Row $row) {

                $row->column(12, function (Column $column) {
                    $column->append(Dashboard::fax_gonder());
                });
            });
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:4096'
        ]);

        if ($validator->fails()) {
            $data = [
                'status'  => false,
                'message' => $validator->errors()->first('file'),
            ];
            return response()->json($data);
        }

        if($request->hasFile('file')) {
            try {
                $name = 'fax_gonderilecek.pdf';
                $file = $request->file('file');
                $file->move(public_path('uploads'), $name);

                $data = [
                    'status' => true,
                    'message' => 'Yeni Pdf Dosyası Yüklendi!',
                ];
            }
            catch (\Exception $err) {
                $data = [
                    'status' => false,
                    'message' => 'Pdf Dosyası Yüklenemedi! ' . $err,
                ];
            }
        }
        else {
            $data = [
                'status'  => false,
                'message' => 'Pdf Dosyası Yüklenemedi!',
            ];
        }

        return response()->json($data);
    }
}
