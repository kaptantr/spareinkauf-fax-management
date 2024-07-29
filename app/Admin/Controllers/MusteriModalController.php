<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Document\CustomRowAction;
use App\Models\MusteriTanim;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MusteriModalController extends Controller
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
            ->title('Müşteri Kayıtları&emsp;')
            ->header('Müşteri Kayıtları&nbsp;&nbsp;>&nbsp; Listele')
            ->description(' ')
            ->body($this->grid());
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MusteriTanim);

        //$grid->column('id', 'ID')->sortable();
        $grid->column('adsoyad', 'Müşteri Adı Soyadı')->display(function () { return $this->ad . ' ' . $this->soyad; })->sortable();
        $grid->column('il_ilce', 'İl / İlçe')->display(function () { return $this->il . ' ' . $this->ulke; })->sortable();
        $grid->tel('Telefon');
        $grid->fax('Fax No');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->add(new CustomRowAction($actions->row));
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->disableCreateButton();
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->expandFilter();

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('ad', 'Müşteri Adı');
            $filter->like('soyad', 'Müşteri Soyadı');
        });

        $grid->setActionClass(Grid\Displayers\DropdownActions::class);

        $grid->paginate(10);

        return $grid;
    }

}
