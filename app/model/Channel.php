<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Channel extends Model
{
    protected $table = 'channel';
    protected $pk = 'id';
}
