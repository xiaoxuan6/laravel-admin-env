<?php

namespace James\Env\Http\Extensions\Tool;
use Encore\Admin\Grid\Tools\BatchAction;

class DeleteAll extends BatchAction
{
    protected $url;
    protected $page;

    public function __construct($url = '', $page)
    {
        $this->url = $url;
        $this->page = $page;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {
    var ids = selectedRows().join();
    if(!ids){
        swal({
        title: "请选择操作对象!",
        type: "warning",
        showCancelButton: false,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确认",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        });
        return false;
    }
        
    $.ajax({
        method: 'post',
        url: '{$this->url}',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            page: {$this->page}
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            toastr.success('操作成功');
        }
    });
});

EOT;

    }
}