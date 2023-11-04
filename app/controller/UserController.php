<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Address;
use app\model\Channel;
use app\model\Order;
use app\model\Parcel;
use app\validate\applytableinfor;
use think\exception\ValidateException;
use think\facade\Cookie;
use think\facade\Db;
use think\facade\Session;
use think\Request;
use think\response\File;
use think\response\Json;
use think\response\Redirect;
use think\response\View;

class UserController extends BaseController
{
    public function index(): View
    {
        return view('user/index');
    }

    public function Applyinformation(): View
    {
        return view('user/Applyinformation');
    }

    public function postapply(Request $request): Json
    {
        $postinformation = array();
        $postinformation['Name'] = $request->param('Name');
        $postinformation['Address'] = $request->param('Address');
        $postinformation['CityRegion'] = $request->param('CityRegion');
        $postinformation['PostalCode'] = $request->param('PostalCode');
        $postinformation['Age'] = $request->param('Age');
        $postinformation['Occupation'] = $request->param('Occupation');
        $postinformation['FamilyMembers'] = $request->param('FamilyMembers');
        $postinformation['FamilyMemberDetails'] = $request->param('FamilyMemberDetails');
        $postinformation['userid'] = Session::get('userID');
        $postinformation['FamilyIncome'] = $request->param('FamilyIncome');
        $postinformation['HousingType'] = $request->param('HousingType');
        $postinformation['AllowingPets'] = $request->param('AllowingPets');
        $postinformation['ApartmentPetPolicy'] = $request->param('ApartmentPetPolicy');
        $postinformation['ApartmentPolicyDetails'] = $request->param('ApartmentPolicyDetails');
        $postinformation['PetExperience'] = $request->param('PetExperience');
        $postinformation['PreviousPets'] = $request->param('PreviousPets');
        $postinformation['ReasonToAdopt'] = $request->param('ReasonToAdopt');
        $postinformation['PetCareAndTrainingPlan'] = $request->param('PetCareAndTrainingPlan');
        $postinformation['DailyScheduleImpact'] = $request->param('DailyScheduleImpact');
        $postinformation['DailyTimeWithDog'] = $request->param('DailyTimeWithDog');
        $postinformation['ExerciseAndPlay'] = $request->param('ExerciseAndPlay');
        $postinformation['VeterinaryCare'] = $request->param('VeterinaryCare');
        $postinformation['HomeVisit'] = $request->param('HomeVisit');
        $postinformation['AdditionalInfo'] = $request->param('AdditionalInfo');
        return $this->app->ApplyService->applytable($postinformation);
    }
}
