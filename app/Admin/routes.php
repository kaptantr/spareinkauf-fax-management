<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'DashboardController@index');

    $router->get('/siparisler', 'SiparisController@index')->name('siparisler');
    $router->get('/siparis-ekle', 'SiparisEkleController@index')->name('siparis-ekle');
    $router->get('/siparisler/siparis-detay/{siparis_id}', 'SiparisDetayController@index')->name('siparis-detay');
    $router->delete('/siparisler/siparis-sil/{siparis_id}', 'SiparisController@destroy')->name('siparis-sil');
    $router->post('/siparisler/musteri-degistir', 'SiparisController@musteri_degistir')->name('musteri-degistir');
    $router->post('/siparisler/stok-kart-degistir', 'SiparisController@stok_kart_degistir')->name('stok-kart-degistir');

    $router->post('/siparisler/woocommerce-urun-esitle', 'SiparisController@woocommerce_urun_esitle')->name('woocommerce-urun-esitle');

    $router->post('/siparisler/faks-esitle', 'SiparisController@fakstan_esitle')->name('faks-esitle');

    $router->post('/siparisler/woocommerce-siparis-olustur', 'SiparisController@woocommerce_siparis_olustur')->name('woocommerce-siparis-olustur');
    $router->post('/siparisler/woocommerce-siparis-kaydet', 'SiparisController@woocommerce_siparis_kaydet')->name('woocommerce-siparis-kaydet');
    $router->post('/siparisler/woocommerce-siparis-update', 'SiparisController@woocommerce_siparis_guncelle')->name('woocommerce-siparis-update');

    $router->get('/fax-gonder', 'FaxGonderController@index')->name('fax-gonder');
    $router->post('/fax-yukle', 'FaxGonderController@store')->name('fax-yukle');

    $router->get('/magazadan-urun-esitle', 'MagazadanUrunEsitleController@index')->name('magazadan-urun-esitle');
    $router->get('/fakstan-esitle', 'FakstanEsitleController@index')->name('fakstan-esitle');
    $router->get('/stok-kartlari', 'SiparisController@stok_kartlari')->name('stok-kartlari');

    //$router->resource('/stok-kartlari', 'StokKartController');

    $router->resource('/musteri-kayitlari', 'MusteriTanimController');

    $router->get('/musteri-modal', 'MusteriModalController@index')->name('musteri-modal');

});
