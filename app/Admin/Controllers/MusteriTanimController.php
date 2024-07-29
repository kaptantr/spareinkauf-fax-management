<?php

namespace App\Admin\Controllers;

use App\Models\MusteriTanim;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MusteriTanimController extends Controller
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
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title('Müşteri Kayıtları&emsp;')
            ->header('Müşteri Kayıtları&nbsp;&nbsp;>&nbsp; Değiştir')
            ->description(' ')
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
            ->title('Müşteri Kayıtları&emsp;')
            ->header('Müşteri Kayıtları&nbsp;&nbsp;>&nbsp; Oluştur')
            ->description(' ')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MusteriTanim);

        $grid->column('id', 'ID')->sortable();
        //$grid->hitap_sekli('Hitap Şekli')->sortable();
        //$grid->baslik('Başlık')->sortable();
        $grid->ad('Müşteri Adı')->sortable();
        $grid->soyad('Soyadı')->sortable();
        //$grid->konu_alani('Konu Alanı');
        $grid->il('İl')->sortable();
        $grid->ulke('Ülke')->sortable();
        //$grid->posta_kodu('Posta Kodu');
        //$grid->siniflandirma('Sınıflandırma',);
        //$grid->derecelendirme('Derecelendirme');
        //$grid->degerlendirme_havuzu('Değerlendirme Havuzu');
        //$grid->acik_mi('Açık mı?');
        //->mesai_baslangic('Mesai Başlangıç');
        //$grid->mesai_bitis('Mesai Bitiş');
        $grid->tel('Telefon');
        $grid->fax('Fax No');
        //$grid->web_site('Web Site');
        //$grid->teslimat_il('Teslimat İl');
        //$grid->teslimat_ilce('Teslimat İlce');
        //$grid->teslimat_telefon_no('Teslimat Telefon');
        //$grid->fax_arama_field('Fax Arama Alanı');
        //$grid->teslimat_posta_kodu('Teslimat Posta Kodu');
        $grid->mail('Mail');
        //$grid->kara_liste('Kara Liste');
        //$grid->fax_numara_guncellenmis('Fax Numara Güncellenmiş');

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

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


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MusteriTanim);

        //$form->display('ID');
        $form->text('hitap_sekli', 'Hitap Şekli:')->rules('nullable|max:10');
        $form->text('baslik', 'Başlık:')->rules('nullable|max:255');
        $form->text('ad', 'Müşteri Adı:')->rules('nullable|max:100');
        $form->text('soyad', 'Müşteri Soyadı:')->rules('nullable|max:100');
        $form->text('konu_alani', 'Konu Alanı:')->rules('nullable|max:255');
        $form->text('il', 'İl:')->rules('nullable|max:50');
        $form->text('ilce', 'İlce:')->rules('nullable|max:50');
        $form->text('ulke', 'Ülke:')->rules('nullable|max:50');
        $form->text('posta_kodu', 'Posta Kodu:')->rules('nullable|max:10');
        $form->decimal('siniflandirma', 'Sınıflandırma:')->value('0');
        $form->number('derecelendirme', 'Derecelendirme:')->min(0)->value('0');
        //$form->text('degerlendirme_havuzu', 'Değerlendirme Havuzu:')->rules('nullable|max:100');
        $form->switch('acik_mi', 'Açık_mı?:');
        $form->time('mesai_baslangic', 'Mesai Başlangıç:');
        $form->time('mesai_bitis', 'Mesai Bitiş:');
        $form->text('tel', 'Telefon:')->rules('nullable|max:25');
        $form->text('fax', 'Fax No:')->rules('nullable|max:25');
        $form->text('web_site', 'Web Site:')->rules('nullable|max:255');
        $form->text('teslimat_il', 'Teslimat İl:')->rules('nullable|max:50');
        $form->text('teslimat_ilce', 'Teslimat İlçe:')->rules('nullable|max:50');
        $form->text('teslimat_ulke', 'Teslimat Ülke:')->rules('nullable|max:50');
        $form->text('teslimat_adres', 'Teslimat Adresi:')->rules('nullable|max:255');
        $form->text('teslimat_telefon_no', 'Teslimat Telefon No:')->rules('nullable|max:25');
        $form->text('teslimat_posta_kodu', 'Teslimat Posta Kodu:')->rules('nullable|max:10');
        $form->text('teslimat_fax_no', 'Teslimat Fax Kodu:')->rules('nullable|max:50');
        $form->text('mail', 'Mail Adresi:')->rules('nullable|max:255');
        $form->switch('kara_liste', 'Kara Listede mi?:');
        $form->text('fax_numara_guncellenmis', 'Fax Numara Güncellenmiş:')->rules('nullable|max:25');

        $form->tools(function (Form\Tools $tools) {
            //$tools->disableList();
            //$tools->disableDelete();
            $tools->disableView();
        });

        $form->footer(function ($footer) {
            //$footer->disableReset();
            //$footer->disableSubmit();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        return $form;
    }
}
