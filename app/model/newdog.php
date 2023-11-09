<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class newdog extends Model
{
    protected $table = 'newdog';

    protected $pk = 'id';
}
