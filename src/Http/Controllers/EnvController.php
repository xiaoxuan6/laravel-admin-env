<?php

namespace James\Env\Http\Controllers;

use Encore\Admin\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Show;
use James\Env\EnvModel;
use James\Env\Http\Extensions\Tool\DeleteAll;
use Illuminate\Support\Facades\Request as Requests;

class EnvController extends Controller
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
            ->header('Env')
            ->description('')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Env')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Env')
            ->description('详情')
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
            ->header('Env')
            ->description('新增')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $page = Requests::get('page', 1);
        $key = Requests::get('key');
        $grid = new Grid(new EnvModel);

        $grid->key();
        $grid->value();
        $grid->disableExport();
        $grid->actions(function ($actions) use ($page){
            $id = $actions->getKey();
            $actions->disableView();
            $actions->disableDelete();
            $actions->append("<a href='/admin/env/{$id}/delete?page={$page}' class='' data-id='{$id}'><i class='fa fa-trash'></i></a>");
        });
        $grid->tools(function($tools) use ($page, $key){
            $tools->batch(function ($batch) use ($page){
                $batch->add('删除', new DeleteAll('/admin/env/delete-all', $page));
                $batch->disableDelete();
            });
            $tools->append(view('env::admin.search', compact('key')));
        });
        $grid->disableFilter();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(EnvModel::findOrFail($id));

        $show->id('ID');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EnvModel);

        $form->text('key', 'Key');
        $form->text('value', 'Value');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        return $form;
    }

    public function delete(Request $request, $id){
        $page = $request->input('page');

        if(!EnvModel::isDel($id))
            admin_toastr('操作失败', 'error');
        else
            admin_toastr('操作成功','success');

        return redirect('/admin/env?page='.$page);
    }

    public function deleteAll(Request $request){
        $ids = $request->input('ids');
        $page = $request->input('page');

        if(!EnvModel::isDel($ids))
            admin_toastr('操作失败', 'error');
        else
            admin_toastr('操作成功','success');

        return redirect('/admin/env?page='.$page);
    }
}