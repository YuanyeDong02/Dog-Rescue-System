<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin Model
 */
class Token extends Model
{
    protected $table = 'user_token';
    protected $pk = 'id';
}
