<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class dogtableinfor extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        "Name" => "require",
        "Breed" => "require",
        'Age' => 'require|number|between:1,100',
        "Gender" => "require",
        "Color" => "require",
        "Weight" => "require",
        "SpayedOrNeutered" => "require",
        "VaccinationStatus" => "require",
        "SpecialNeeds" => "require",
        "AdoptionRestrictions" => "require",
        "AdoptionsStatus" => "require",




    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'Name.require' => 'Name cannot be empty',
        'Breed.require' => 'Breed cannot be empty',
        'Age.require' => 'Age cannot be empty',
        'Age.number' => 'Age must be a number',
        'Age.between' => 'Age must be between 1 and 100',
        'Gender.require' =>'Gender cannot be empty',
        'Color.require' => 'Color cannot be empty',
        'Weight.require' => 'Weight cannot be empty',
        'SpayedOrNeutered.require' => 'SpayedOrNeutered cannot be empty',
        'VaccinationStatus.require' => 'VaccinationStatus cannot be empty',
        'SpecialNeeds.require' => 'SpecialNeeds cannot be empty',
        'AdoptionRestrictions.require' => 'AdoptionRestrictions cannot be empty',
        'AdoptionsStatus.require' => 'AdoptionsStatus cannot be empty',


    ];
}
