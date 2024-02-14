<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Apply;
use app\model\newdog;
use app\model\User;
use think\console\Output;
use think\facade\Cookie;
use think\facade\Db;
use think\facade\Session;
use think\helper\Str;
use think\initializer\Error;
use think\Request;
use think\response\File;
use think\response\Json;
use think\response\Redirect;
use think\response\View;

class UserController extends BaseController
{
    public function index(): View
    {
        $dog = new newdog();
        $dog = $dog->field("id")->select();
        return view('user/index',['dogs' => $dog]);
    }

    public function Applyinformation($id): View
    {
        return view('user/Applyinformation',['dogid' => $id]);
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
        $postinformation['PetExperienceDetails'] = $request->param('PetExperienceDetails');
        $postinformation['ReasonToAdopt'] = $request->param('ReasonToAdopt');
        $postinformation['PetCareAndTrainingPlan'] = $request->param('PetCareAndTrainingPlan');
        $postinformation['DailyScheduleImpact'] = $request->param('DailyScheduleImpact');
        $postinformation['DailyTimeWithDog'] = $request->param('DailyTimeWithDog');
        $postinformation['ExerciseAndPlay'] = $request->param('ExerciseAndPlay');
        $postinformation['VeterinaryCare'] = $request->param('VeterinaryCare');
        $postinformation['HomeVisit'] = $request->param('HomeVisit');
        $postinformation['dogid'] = $request->param('dogid');

        return $this->app->ApplyService->applytable($postinformation);
    }

    public function Applyprogress(): View
    {
        $apply = new Apply();
        $apply = $apply->where('userid', Session::get('userID'))->limit(1)->findOrEmpty();
        if ($apply->isEmpty()) {
            $step = 0;

        } elseif ($apply->active == 0 and $apply->statuses == 0) {
            $step = 1;
        }else{
            $step = 2;
    }
        return view('user/Applyprogress', ['step' => $step,'result'=>$apply->result]);
    }

    public function Applyfinished(): View
    {
        $apply = Db::table('apply')->where('userid', Session::get('userID'))->find();

        return view('user/Applyfinished', ['apply' => $apply]);
    }

    public function viewapply()
    {

        $apply = Apply::where('userid', Session::get('userID'))
            ->order('created_at', 'desc')
            ->limit(1)->findOrEmpty();

        if ($apply === null) {
            throw new \Exception('No application found for the user.');
        } else {

            return view('user/settings', ['apply' => $apply]);
        }
    }


    public function logout(): Redirect
    {
        Session::delete('userID');
        Cookie::delete('userID');
        return redirect('/auth/login');
    }
    public function dogname($id): ?File
    {
        $photo = Db::table('newdog')->where('id', $id)->select("image");
        if ($photo->isEmpty()) {
            return null;
        } else {
            $filepath = app()->getRootPath() . "storage/". $photo[0]["image"];
            $file = new File($filepath);
            return $file;
        }

    }
    public function dogdetail($id): View
    {
        $dog = newdog::where('id', $id)->find();
        return view('user/dogdetail', ['dog' => $dog]);
    }

    public function savetime(Request $request): Json
    {
        $userid = Session::get('userID');
        $time = $request->param('time');
        $result = Db::name('selecttime')
        ->insert([
            'userid' => $userid,
            'time' => $time,
            'link' => Str::random(20)
        ]);
        //获取用户邮箱
        $user = User::where('id', $userid)->findOrEmpty();
        if ($user->isEmpty()) {
            return json([
                'msg' => "User does not exist",
                'ret' => 0
            ]);
        }
        $email = $user->email;
        $this->app->userService->Timebooking($email, $userid);



        if ($result) {

            return json(['status' => 'success', 'message' => 'Time saved successfully']);
        } else {
            return json(['status' => 'error', 'message' => 'Failed to save time']);
        }

    }
    public function videocall (): View
    {
        $userid = Session::get('userID');
        $code = Db::name('selecttime')->where('userid', $userid)->order('id', 'desc')->value('link');

        if ($code === null) {
            return view('error', ['message' => 'You have not booked a time yet. Please apply and book a time first.']);
        }
        else{
            return view('user/videocall', ['code' => $code]);
        }
    }

}
