<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Apply extends Model
{
    protected $table = 'apply';
    protected $pk = 'id';
}
