<?php

namespace App\Admin\Controllers;

use App\Models\StokTanim;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class StokTanimController extends Controller
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
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StokTanim);

        $grid->id('ID');
        $grid->siparis_id('siparis_id');
        $grid->stok_kart_adi('stok_kart_adi');
        $grid->stok_kart_kodu('stok_kart_kodu');
        $grid->adet('adet');
        //$grid->magento_id('magento_id');
        //$grid->shopware_id('shopware_id');
        //$grid->shopify_id('shopify_id');
        $grid->shopify_id('woocommerce_id');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(StokTanim::findOrFail($id));

        $show->id('ID');
        $show->siparis_id('siparis_id');
        $show->stok_kart_adi('stok_kart_adi');
        $show->stok_kart_kodu('stok_kart_kodu');
        $show->adet('adet');
        //$show->magento_id('magento_id');
        //$show->shopware_id('shopware_id');
        //$show->shopify_id('shopify_id');
        $show->shopify_id('woocommerce_id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StokTanim);

        $form->display('ID');
        $form->text('siparis_id', 'siparis_id');
        $form->text('stok_kart_adi', 'stok_kart_adi');
        $form->text('stok_kart_kodu', 'stok_kart_kodu');
        $form->text('adet', 'adet');
        //$form->text('magento_id', 'magento_id');
        //$form->text('shopware_id', 'shopware_id');
        //$form->text('shopify_id', 'shopify_id');
        $form->text('woocommerce_id', 'woocommerce_id');

        return $form;
    }
}
