<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class OrderAddress extends Model
{
    protected $table = 'order_address';
    protected $pk = 'id';
}
