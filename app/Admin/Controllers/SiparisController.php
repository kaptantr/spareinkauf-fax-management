<?php

namespace App\Admin\Controllers;

use App\Models\MagazaStok;
use App\Models\MusteriTanim;
use App\Models\Siparis;
use App\Models\StokKart;
use App\Models\StokTanim;
use App\Http\Controllers\Controller;
use Codexshaper\WooCommerce\Models\Order;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Codexshaper\WooCommerce\Facades\Product;
use DB;

class SiparisController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->title('Siparişler&emsp;')
            ->header('Siparişler&nbsp;>&nbsp; Listele')
            ->description(' ')
            ->body($this->grid());
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ids = explode(',', $id);

        if (Siparis::destroy(array_filter($ids))) {
            $data = [
                'status' => true,
                'then' => ['action' => 'refresh', 'value' => true],
                'swal' => ['type' => 'success', 'title' => trans('admin.delete_succeeded')]
            ];
        } else {
            $data = [
                'status' => false,
                'then' => ['action' => 'refresh', 'value' => true],
                'swal' => ['type' => 'success', 'title' => trans('admin.delete_failed')]
            ];
        }

        return response()->json($data);
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Siparis);

        $grid->column('id', 'ID')->sortable();
        $grid->column('tarih', 'Sipariş Tarihi')->sortable();
        $grid->column('adsoyad', 'Müşteri Adı Soyadı')->display(function () { return ($this->musteri->ad ?? '') . ' ' . ($this->musteri->soyad ?? ''); });
        $grid->column('musteri.tel', 'Telefon Numarası')->sortable();
        $grid->column('il_ulke', 'İl / Ülke')->display(function () { return (!empty($this->musteri->il) ? $this->musteri->il . '/' : ''). $this->musteri->ulke; });
        $grid->stoks('Sipariş Edilen Ürünler')->display(function ($stoks) {
            $stoks = collect($stoks)->map(function ($stok) {
                $kart_adi = MagazaStok::where('id', $stok['kart_id'])->pluck('urun_adi')->first();
                return "<b>{$stok['adet']}</b> * " . $kart_adi ?? '';
            })->toArray();

            return implode('<br><br>', $stoks);
        })->style('white-space: nowrap;');

        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });

        $grid->setActionClass(Grid\Displayers\DropdownActions::class);

        $grid->model()->orderBy('tarih', 'desc');

        $grid->paginate(10);

        return $grid;
    }

    public function stok_kartlari(Content $content)
    {
        return $content
            ->title('Siparişler&emsp;')
            ->header('Siparişler&nbsp;&nbsp;>&nbsp;Stok Kalemleri')
            ->description(' ')
            ->row(function (Row $row) {

                $row->column(12, function (Column $column) {
                    $column->append(Dashboard::stok_kartlari_tablosu());
                });
            });
    }

    public function stok_kart_degistir(Request $request)
    {
        $input = $request->all();
        $magaza_stok_id = $input['magaza_stok_id'];
        $durum = $input['durum'];

        if(!empty($magaza_stok_id)) {

            try {
                $siparis = MagazaStok::find($magaza_stok_id)->update(['stok_karti' => $durum]);

                $data = [
                    'status' => true,
                    'message' => ($durum ? 'Stok Kalemine eklendi!' : 'Stok Kaleminden çıkarıldı!'),
                ];
            }
            catch (\Exception $err) {
                $data = [
                    'status' => false,
                    'message' => 'Stok Kalemine eklenemedi!. ' . $err,
                ];
            }
        }
        else {
            $data = [
                'status'  => false,
                'message' => 'Stok Kalemine eklenemedi!.',
            ];
        }

        return response()->json($data);
    }

    public function musteri_degistir(Request $request)
    {
        $input = $request->all();
        $siparis_id = $input['siparis_id'];
        $musteri_id = $input['musteri_id'];

        if(!empty($siparis_id) && !empty($musteri_id)) {

            try {
                $siparis = Siparis::find($siparis_id)->update(['musteri_id' => $musteri_id]);

                $data = [
                    'status' => true,
                    'message' => 'Müşteri bilgileri kaydedildi!',
                ];
            }
            catch (\Exception $err) {
                $data = [
                    'status' => false,
                    'message' => 'Seçilen müşteri bilgileri kaydedilemedi. ' . $err,
                ];
            }
        }
        else {
            $data = [
                'status'  => false,
                'message' => 'Seçilen müşteri bilgileri kaydedilemedi!',
            ];
        }

        return response()->json($data);
    }


    public function woocommerce_urun_esitle()
    {

        try {
            $urunler = Product::all(['per_page' => 100]);

            DB::beginTransaction();

            foreach ($urunler as $urun) {
                $woocommerce_id = $urun->id ?? 0;
                if(empty($woocommerce_id)) { continue; }

                $input = [
                    'woocommerce_id' => $woocommerce_id,
                    'urun_adi' => $urun->name,
                    'urun_link' => $urun->permalink,
                    'stok_durumu' => $urun->stock_status=='instock' ? '1' : '0',
                    'urun_sku' => $urun->sku,
                    'adet' => !empty($urun->stock_quantity) ? $urun->stock_quantity : '0',
                    'fiyat' => !empty($urun->price) ? $urun->price : '0',
                    'magaza_adresi' => trim(env('WOOCOMMERCE_STORE_URL'), '/'),
                    'esitleme_tarihi' => date('Y-m-d H:i:s', strtotime('+3 hours')),
                ];

                $magaza_stok = MagazaStok::updateOrCreate([ 'woocommerce_id' => $woocommerce_id ], $input);
            }

            DB::commit();

            $data = [
                'status' => true,
                'message' => 'Mağaza ile eşitleme sağlandı ve ürünler kaydedildi!',
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();
            $data = [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($data);
    }


    public function woocommerce_siparis_kaydet(Request $request)
    {
        $discount = false;
        $input = $request->all();
        /*$input = [
            'kart_6358' => 10,
            'kart_6327' => 10,
            'siparis_id' => 21415,
            'musteri_id' => 405043,
            'musteri_ad' => 'Tahido',
            'musteri_soyad' => 'Test',
            'musteri_tel' => '+4922166960890',
            'musteri_fax' => '+4922166960890',
            'musteri_il' => 'Test',
            'musteri_ulke' => 'Deutschland',
            'musteri_adres' => '',
            'musteri_posta_kodu' => 54321,
            'musteri_mail' => 'example@domain.com',
            'musteri_teslimat_il' => 'Test',
            'musteri_teslimat_ulke' => 'Deutschland',
            'musteri_teslimat_telefon_no' => '+4922166960890',
            'musteri_teslimat_fax_no' => '+4922166960890',
            'musteri_teslimat_adres' => '',
            'musteri_teslimat_posta_kodu' => 54321,
        ];*/

        $siparis_id = $input['siparis_id'];
        $musteri_id = $input['musteri_id'];

        if(!empty($siparis_id) && !empty($musteri_id)) {

            try {
                $siparis = Siparis::where('id', $siparis_id)->first();
                if(empty($siparis)) {
                    $data = [
                        'status' => false,
                        'message' => 'Sipariş bulunamadı!',
                    ];
                    return response()->json($data);
                }

                $musteri = MusteriTanim::where('id', $musteri_id)->first();
                if(empty($musteri)) {
                    $data = [
                        'status' => false,
                        'message' => 'Müşteri bulunamadı!',
                    ];
                    return response()->json($data);
                }
                $musteri = MusteriTanim::find($musteri_id)->update(
                    [
                        'ad' => $input['musteri_ad'],
                        'soyad' => $input['musteri_soyad'],
                        'tel' => $input['musteri_tel'],
                        'fax' => $input['musteri_fax'],
                        'adres' => $input['musteri_adres'],
                        'il' => $input['musteri_il'],
                        'ulke' => $input['musteri_ulke'],
                        'posta_kodu' => $input['musteri_posta_kodu'],
                        'mail' => $input['musteri_mail'],
                        'teslimat_il' => $input['musteri_teslimat_il'],
                        'teslimat_ulke' => $input['musteri_teslimat_ulke'],
                        'teslimat_telefon_no' => $input['musteri_teslimat_telefon_no'],
                        'teslimat_fax_no' => $input['musteri_teslimat_fax_no'],
                        'teslimat_adres' => $input['musteri_teslimat_adres'],
                        'teslimat_posta_kodu' => $input['musteri_teslimat_posta_kodu'],
                    ]
                );

                $data = [
                    'payment_method'       => 'bacs',
                    'payment_method_title' => '',
                    'set_paid'             => false,
                    'status'               => 'pending',
                    'currency'             => 'EUR',
                    'billing' => [
                        'first_name' => $input['musteri_ad'] ?: '',
                        'last_name'  => $input['musteri_soyad'] ?: '',
                        'address_1'  => $input['musteri_adres'] ?: '',
                        'address_2'  => '',
                        'city'       => $input['musteri_il'] ?: '',
                        'state'      => '',
                        'postcode'   => $input['musteri_posta_kodu'] ?: '',
                        'country'    => $input['musteri_ulke'] ?: '',
                        'email'      => /*filter_var($input['musteri_mail'], FILTER_VALIDATE_EMAIL) ? */$input['musteri_mail']/* : ''*/,
                    ],
                    'shipping' => [
                        'first_name' => $input['musteri_ad'] ?: '',
                        'last_name'  => $input['musteri_soyad'] ?: '',
                        'address_1'  => $input['musteri_teslimat_adres'] ?: '',
                        'address_2'  => '',
                        'city'       => $input['musteri_il'] ?: '',
                        'state'      => '',
                        'postcode'   => $input['musteri_teslimat_posta_kodu'] ?: '',
                        'country'    => $input['musteri_teslimat_ulke'] ?: '',
                    ],
                    'line_items' => [ ],
                ];

                foreach ($input as $key=>$val) {
                    if(str_starts_with($key, 'kart_')) {
                        $product_id = trim(str_ireplace('kart_', '', $key));
                        $quantity = $val;
                        $data['line_items'][] = [ 'product_id' => $product_id, 'quantity' => $quantity ];
                    }

                }

                //exit(print_r($data,1));

                $order = Order::create($data);

                foreach ($data['line_items'] as $item) {
                    $product_id = $item['product_id'];
                    $quantity = $item['quantity'];

                    $magaza = MagazaStok::where('woocommerce_id', $product_id)->first();
                    if(!empty($magaza)) {
                        $stok_tanim = StokTanim::updateOrCreate(
                            [
                                'siparis_id' => $siparis_id,
                                'kart_id' => $magaza->id,
                                'woocommerce_id' => $product_id
                            ],
                            [
                                'siparis_id' => $siparis_id,
                                'kart_id' => $magaza->id,
                                'adet' => $quantity,
                                'woocommerce_id' => $product_id
                            ]
                        );
                    }
                }

                $siparis_ = Siparis::find($siparis_id)->update(['siparis_islendimi' => '1', 'response_json' => json_encode($order)]);

                $data = [
                    'status' => true,
                    'message' => 'Sipariş ve kartları mağazaya kaydedildi!',
                ];
            }
            catch (\Exception $err) {
                $data = [
                    'status' => false,
                    'message' => 'Sipariş ve kartları mağazaya kaydedilemedi. ' . $err,
                ];
            }

        }
        else {
            $data = [
                'status'  => false,
                'message' => 'Sipariş ve kartları mağazaya kaydedilemedi!',
            ];
        }

        return response()->json($data);

    }

    public function woocommerce_siparis_olustur(Request $request)
    {
        $input = $request->all();

        $siparis_id = $input['siparis_id'];
        $musteri_id = $input['musteri_id'];

        if(isset($siparis_id) && $siparis_id == '-1' && !empty($musteri_id)) {
            //exit(print_r($input,1));

            try {
                $musteri = MusteriTanim::where('id', $musteri_id)->first();
                if(empty($musteri)) {
                    $data = [
                        'status' => false,
                        'message' => 'Müşteri bulunamadı!',
                    ];
                    return response()->json($data);
                }
                $musteri = MusteriTanim::find($musteri_id)->update(
                    [
                        'ad' => $input['musteri_ad'],
                        'soyad' => $input['musteri_soyad'],
                        'tel' => $input['musteri_tel'],
                        'fax' => $input['musteri_fax'],
                        'adres' => $input['musteri_adres'],
                        'il' => $input['musteri_il'],
                        'ulke' => $input['musteri_ulke'],
                        'posta_kodu' => $input['musteri_posta_kodu'],
                        'mail' => $input['musteri_mail'],
                        'teslimat_il' => $input['musteri_teslimat_il'],
                        'teslimat_ulke' => $input['musteri_teslimat_ulke'],
                        'teslimat_telefon_no' => $input['musteri_teslimat_telefon_no'],
                        'teslimat_fax_no' => $input['musteri_teslimat_fax_no'],
                        'teslimat_adres' => $input['musteri_teslimat_adres'],
                        'teslimat_posta_kodu' => $input['musteri_teslimat_posta_kodu'],
                    ]
                );

                $data = [
                    'payment_method'       => 'bacs',
                    'payment_method_title' => '',
                    'set_paid'             => false,
                    'status'               => 'pending',
                    'currency'             => 'EUR',
                    'billing' => [
                        'first_name' => $input['musteri_ad'] ?: '',
                        'last_name'  => $input['musteri_soyad'] ?: '',
                        'address_1'  => $input['musteri_adres'] ?: '',
                        'address_2'  => '',
                        'city'       => $input['musteri_il'] ?: '',
                        'state'      => '',
                        'postcode'   => $input['musteri_posta_kodu'] ?: '',
                        'country'    => $input['musteri_ulke'] ?: '',
                        'email'      => $input['musteri_mail'],
                    ],
                    'shipping' => [
                        'first_name' => $input['musteri_ad'] ?: '',
                        'last_name'  => $input['musteri_soyad'] ?: '',
                        'address_1'  => $input['musteri_teslimat_adres'] ?: '',
                        'address_2'  => '',
                        'city'       => $input['musteri_il'] ?: '',
                        'state'      => '',
                        'postcode'   => $input['musteri_teslimat_posta_kodu'] ?: '',
                        'country'    => $input['musteri_teslimat_ulke'] ?: '',
                    ],
                    'line_items' => [ ],
                ];

                foreach ($input as $key=>$val) {
                    if(str_starts_with($key, 'kart_')) {
                        $product_id = str_ireplace('kart_', '', $key);
                        $quantity = $val;
                        $data['line_items'][] = [ 'product_id' => $product_id, 'quantity' => $quantity ];
                    }

                }

                //exit(print_r($data,1));
                $order = Order::create($data);

                $siparis = Siparis::create(
                    [
                        'musteri_id' => $musteri_id,
                        'siparis_islendimi' => '1',
                        'tarih' => date('Y-m-d H:i:s', strtotime('+3 hours')),
                        'pdf_adi' => 'default.pdf',
                        'listelensin' => '1',
                        'response_json' => json_encode($order)
                    ]
                );


                foreach ($data['line_items'] as $item) {
                    $product_id = $item['product_id'];
                    $quantity = $item['quantity'];

                    $magaza = MagazaStok::where('woocommerce_id', $product_id)->first();
                    if(!empty($magaza)) {
                        $stok_tanim = StokTanim::updateOrCreate(
                            [
                                'siparis_id' => $siparis->id,
                                'kart_id' => $magaza->id,
                                'woocommerce_id' => $product_id
                            ],
                            [
                                'siparis_id' => $siparis->id,
                                'kart_id' => $magaza->id,
                                'adet' => $quantity,
                                'woocommerce_id' => $product_id
                            ]
                        );
                    }
                }

                $data = [
                    'status' => true,
                    'message' => 'Yeni sipariş ve kartları mağazaya kaydedildi!',
                ];
            }
            catch (\Exception $err) {
                $data = [
                    'status' => false,
                    'message' => 'Yeni sipariş ve kartları mağazaya kaydedilemedi. ' . $err,
                ];
            }

        }
        else {
            $data = [
                'status'  => false,
                'message' => 'Yeni sipariş ve kartları mağazaya kaydedilemedi!',
            ];
        }

        return response()->json($data);

    }


    public function woocommerce_siparis_guncelle(Request $request)
    {
        $input = $request->all();

        $siparis_id = $input['siparis_id'];
        $woo_siparis_id = $input['woo_siparis_id'];
        $musteri_id = $input['musteri_id'];

        if(!empty($siparis_id) && !empty($musteri_id) && !empty($woo_siparis_id)) {

            //try {
                $siparis = Siparis::where('id', $siparis_id)->first();
                if(empty($siparis)) {
                    $data = [
                        'status' => false,
                        'message' => 'Sipariş bulunamadı!',
                    ];
                    return response()->json($data);
                }
                if(empty($siparis->response_json)) {
                    $data = [
                        'status' => false,
                        'message' => 'Sipariş için Response_Json bulunamadı!',
                    ];
                    return response()->json($data);
                }

                $data_json = json_decode($siparis->response_json);
                $items = $data_json->line_items;


                $musteri = MusteriTanim::where('id', $musteri_id)->first();
                if(empty($musteri)) {
                    $data = [
                        'status' => false,
                        'message' => 'Müşteri bulunamadı!',
                    ];
                    return response()->json($data);
                }
                $musteri = MusteriTanim::find($musteri_id)->update(
                    [
                        'ad' => $input['musteri_ad'],
                        'soyad' => $input['musteri_soyad'],
                        'tel' => $input['musteri_tel'],
                        'fax' => $input['musteri_fax'],
                        'adres' => $input['musteri_adres'],
                        'il' => $input['musteri_il'],
                        'ulke' => $input['musteri_ulke'],
                        'posta_kodu' => $input['musteri_posta_kodu'],
                        'mail' => $input['musteri_mail'],
                        'teslimat_il' => $input['musteri_teslimat_il'],
                        'teslimat_ulke' => $input['musteri_teslimat_ulke'],
                        'teslimat_telefon_no' => $input['musteri_teslimat_telefon_no'],
                        'teslimat_fax_no' => $input['musteri_teslimat_fax_no'],
                        'teslimat_adres' => $input['musteri_teslimat_adres'],
                        'teslimat_posta_kodu' => $input['musteri_teslimat_posta_kodu'],
                    ]
                );

                $data = [
                    'billing' => [
                        'first_name' => $input['musteri_ad'] ?: '',
                        'last_name'  => $input['musteri_soyad'] ?: '',
                        'address_1'  => $input['musteri_adres'] ?: '',
                        'address_2'  => '',
                        'city'       => $input['musteri_il'] ?: '',
                        'state'      => '',
                        'postcode'   => $input['musteri_posta_kodu'] ?: '',
                        'country'    => $input['musteri_ulke'] ?: '',
                        'email'      => $input['musteri_mail'],
                    ],
                    'shipping' => [
                        'first_name' => $input['musteri_ad'] ?: '',
                        'last_name'  => $input['musteri_soyad'] ?: '',
                        'address_1'  => $input['musteri_teslimat_adres'] ?: '',
                        'address_2'  => '',
                        'city'       => $input['musteri_il'] ?: '',
                        'state'      => '',
                        'postcode'   => $input['musteri_teslimat_posta_kodu'] ?: '',
                        'country'    => $input['musteri_teslimat_ulke'] ?: '',
                    ],
                    'line_items' => [ ],
                ];

                foreach ($input as $key=>$val) {
                    if(str_starts_with($key, 'kart_')) {
                        $product_id = str_ireplace('kart_', '', $key);
                        $quantity = $val;

                        foreach ($items as $item) {
                            if($item->product_id == $product_id) {
                                $data['line_items'][] = [
                                    'id' => $item->id,
                                    'name' => $item->name,
                                    'quantity' => $quantity,
                                    'subtotal' => number_format(floatval($item->price) * $quantity, 2, '.', ''),
                                    'total' => number_format(floatval($item->price) * $quantity, 2, '.', ''),
                                ];
                                break;
                            }
                        }
                    }

                }

                //exit(print_r($data,1));
                $order = Order::update($woo_siparis_id, $data);

                foreach ($input as $key=>$val) {
                    if(str_starts_with($key, 'kart_')) {
                        $product_id = str_ireplace('kart_', '', $key);
                        $quantity = $val;

                        $magaza = MagazaStok::where('woocommerce_id', $product_id)->first();
                        if(!empty($magaza)) {
                            $stok_tanim = StokTanim::updateOrCreate(
                                [
                                    'siparis_id' => $siparis_id,
                                    'kart_id' => $magaza->id,
                                    'woocommerce_id' => $product_id
                                ],
                                [
                                    'siparis_id' => $siparis_id,
                                    'kart_id' => $magaza->id,
                                    'adet' => $quantity,
                                    'woocommerce_id' => $product_id
                                ]
                            );
                        }
                    }
                }

                $siparis_ = Siparis::find($siparis_id)->update(['siparis_islendimi' => '1', 'response_json' => json_encode($order)]);

                $data = [
                    'status' => true,
                    'message' => 'Sipariş ve kartları mağazaya güncellendi!',
                ];
            //}
            //catch (\Exception $err) {
           //     $data = [
           //         'status' => false,
           //         'message' => 'Sipariş ve kartları mağazaya güncellenemedi. ' . $err,
           //     ];
           // }

        }
        else {
            $data = [
                'status'  => false,
                'message' => 'Sipariş ve kartları mağazaya güncellenemedi!',
            ];
        }

        return response()->json($data);

    }


    public function fakstan_esitle()
    {
        try {

            (new DashboardController)->faks_esitle();

            $data = [
                'status' => true,
                'message' => 'Sistem ile eşitleme sağlandı ve fakslar kaydedildi!',
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();
            $data = [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($data);
    }

}
