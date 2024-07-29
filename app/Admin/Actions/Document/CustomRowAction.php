<?php
namespace App\Admin\Actions\Document;

use Encore\Admin\Actions\RowAction;

class CustomRowAction extends RowAction
{

    protected $row;

    public function __construct($row)
    {
        $this->row = json_encode($row);
    }


    /**
     * @return array|null|string
     */
    public function name()
    {
        return '<a onclick="window.parent.selectRowFax(\'' . base64_encode($this->row) . '\')" href="javascript:void(0);" class="btn2 btn-green py-2 px-4 bg-green" style="border-radius: 5px">
                    <i class="fa fa-check fs-3"></i> Seçiniz
                </a>';
    }

    /**
     * @return string
     */
    public function href()
    {
        return "";
    }
}
