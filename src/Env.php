<?php

namespace James\Env;

use Encore\Admin\Extension;

class Env extends Extension
{
    public $name = 'env';

    public $views = __DIR__.'/../resources/views';

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        parent::createMenu('Env', 'env', 'fa-copy');
        parent::createPermission('Env', 'Env', 'env*');
    }
}