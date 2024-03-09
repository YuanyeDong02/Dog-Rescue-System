<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class applytableinfor extends Validate
{
    /**
     *
     *
     *
     * @var array
     */
    protected $rule = [
        'Name' => 'require',
        'Address' => 'require',
        'CityRegion' => 'require',
        'PostalCode' => 'require',
        'Age' => 'require|number|between:1,100',
        'Occupation' => 'require',
        'FamilyMembers' => 'require|number|between:1,20',
        'FamilyMemberDetails' => 'require',
        'FamilyIncome' => 'require|number|min:0',
        'HousingType' => 'require',
        'AllowingPets' => 'require|boolean',
        'ApartmentPetPolicy' => 'require|boolean',
        'ApartmentPolicyDetails' => 'require',
        'PetExperience' => 'require|boolean',
        'PetExperienceDetails' => 'require',
        'ReasonToAdopt' => 'require',
        'PetCareAndTrainingPlan' => 'require',
        'DailyScheduleImpact' => 'require',
        'DailyTimeWithDog' => 'require|number|between:1,24',
        'ExerciseAndPlay' => 'require|boolean',
        'VeterinaryCare' => 'require|boolean',
        'HomeVisit' => 'require|boolean',




    ];

    /**
     *
     *
     *
     * @var array
     */
    protected $message = [
        'Name.require' => 'Name cannot be empty',
        'Address.require' => 'Address cannot be empty',
        'CityRegion.require' => 'CityRegion cannot be empty',
        'PostalCode.require' => 'PostalCode cannot be empty',
        'Age.require' => 'Age cannot be empty',
        'Age.number' => 'Age must be a number',
        'Age.between' => 'Age must be between 1 and 100',
        'Occupation.require' => 'Occupation cannot be empty',
        'FamilyMembers.require' => 'FamilyMembers cannot be empty',
        'FamilyMembers.number' => 'FamilyMembers must be a number',
        'FamilyMembers.between' => 'FamilyMembers must be between 1 and 20',
        'FamilyMemberDetails.require' => 'FamilyMemberDetails cannot be empty',
        'FamilyIncome.require' => 'FamilyIncome cannot be empty',
        'FamilyIncome.number' => 'FamilyIncome must be a number',
        'FamilyIncome.min' => 'FamilyIncome must be greater than 10',
        'HousingType.require' => 'HousingType cannot be empty',
        'AllowingPets.require' => 'AllowingPets cannot be empty',
        'AllowingPets.boolean' => 'AllowingPets must be a boolean',
        'ApartmentPetPolicy.require' => 'ApartmentPetPolicy cannot be empty',
        'ApartmentPetPolicy.boolean' => 'ApartmentPetPolicy must be a boolean',
        'ApartmentPolicyDetails.require' => 'ApartmentPolicyDetails cannot be empty',
        'PetExperience.require' => 'PetExperience cannot be empty',
        'PetExperience.boolean' => 'Formate of PetExperience is wrong',
        'PetExperienceDetails.require' => 'PetExperienceDetails cannot be empty',
        'ReasonToAdopt.require' => 'ReasonToAdopt cannot be empty',
        'PetCareAndTrainingPlan.require' => 'PetCareAndTrainingPlan cannot be empty',
        'DailyScheduleImpact.require' => 'DailyScheduleImpact cannot be empty',
        'DailyTimeWithDog.require' => 'DailyTimeWithDog cannot be empty',
        'DailyTimeWithDog.number' => 'DailyTimeWithDog must be a number',
        'DailyTimeWithDog.between' => 'DailyTimeWithDog must be between 1 and 24',
        'ExerciseAndPlay.require' => 'ExerciseAndPlay cannot be empty',
        'ExerciseAndPlay.boolean' => 'ExerciseAndPlay must be a boolean',
        'VeterinaryCare.require' => 'VeterinaryCare cannot be empty',
        'VeterinaryCare.boolean' => 'VeterinaryCare must be a boolean',
        'HomeVisit.require' => 'HomeVisit cannot be empty',
        'HomeVisit.boolean' => 'HomeVisit must be a boolean',




    ];
}
