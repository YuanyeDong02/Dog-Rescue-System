<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Address extends Model
{
    protected $table = 'address';
    protected $pk = 'id';
}
