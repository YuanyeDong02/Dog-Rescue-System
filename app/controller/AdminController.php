<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;

use app\model\Apply;
use app\model\User;
use think\facade\Db;
use think\facade\Filesystem;
use think\Request;
use think\response\Json;
use think\response\View;

class AdminController extends BaseController
{
    public function index()
    {
        $apply = Db::table('apply')
            ->join('user', 'apply.userid = user.id')
            ->where('apply.active',0)
            ->field("user.email,apply.Name,apply.id,apply.statuses,apply.result")
        ->select();
        return view('admin/index', [
            'statuses' => $apply
        ]);



    }
    public function applysuccess($id)
    {
        $apply = Apply::where('id', $id)->findOrEmpty();
        if (!$apply->isExists()) {
            return json([
                'msg' => "Apply does not exist",
                'ret' => 0
            ]);
        }
        $apply->result = 1;
        $apply->statuses = 1;
        $apply->save();

    //获取用户邮箱
        $user = User::where('id', $apply->userid)->findOrEmpty();
        if ($user->isEmpty()) {
            return json([
                'msg' => "User does not exist",
                'ret' => 0
            ]);
        }
        $email = $user->email;


        $this->app->userService->Statussucess($email);

        return json(['ret' => 1, 'msg' => 'accept successfully']);

    }
    public function applureject($id)
    {

        $apply = Apply::where('id', $id)->findOrEmpty();
        if ($apply->isEmpty()) {
            return json([
                'msg' => "Apply does not exist",
                'ret' => 0
            ]);
        }
        $apply->result = 0;
        $apply->statuses = 1;
        $apply->save();

        $user = User::where('id', $apply->userid)->findOrEmpty();
        if ($user->isEmpty()) {
            return json([
                'msg' => "User does not exist",
                'ret' => 0
            ]);
        }
        $email = $user->email;


        return $this->app->userService->Statusreject($email);


        return json(['ret' => 1, 'msg' => 'reject successfully']);
    }



    public function viewaplly($id)
    {
        $apply = Apply::where('id', $id)->findOrEmpty();
        $user = User::where('id', $apply->userid)->findOrEmpty();
        return view('admin/applydetail', [
            'apply' => $apply,
            'user' => $user
        ]);
    }

    public function admindogmanagement(): View
    {
        $dogs = Db::table('newdog')->select();
        return view('admin/dogmanagement', [
            'dogs' => $dogs
        ]);
    }
    public function admindogname($id): ?File
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

    public function admindogdetail($id): View
    {
        $dog = Db::table('newdog')->where('id', $id)->find();
        return view('admin/AdminDogDetail', ['dog' => $dog]);
    }


    public function updateDog(Request $request)
    {
        $id = $request->param('id');
        $data = array();
        $data['Name'] = $request->param('Name');
        $data['Breed'] = $request->param('Breed');
        $data['Age'] = $request->param('Age');
        $data['Gender'] = $request->param('Gender');
        $data['Color'] = $request->param('Color');
        $data['Weight'] = $request->param('Weight');
        $data['SpayedOrNeutered'] = $request->param('SpayedOrNeutered');
        $data['VaccinationStatus'] = $request->param('VaccinationStatus');
        $data['SpecialNeeds'] = $request->param('SpecialNeeds');
        $data['AdoptionRestrictions'] = $request->param('AdoptionRestrictions');
        $data['AdoptionStatus'] = $request->param('AdoptionStatus');


        $dog = Db::table('newdog')->where('id', $id)->find();
        if (!$dog) {
            return json(['ret' => 0, 'msg' => 'Dog does not exist']);
        }

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $dog[$key] = $value;
            }
        }


        Db::table('newdog')->where('id', $id)->update($data);




        return json(['ret' => 1, 'msg' => 'Dog updated successfully' ]);
    }


    public function users(): View
    {
        $users = $this->app->userService->getUsers();
        return view('admin/users', [
            'users' => $users
        ]);
    }


    public function editUser(string $id)
    {
        $user = $this->app->userService->getUser(intval($id));
        if (!$user) {
            return redirect('/admin/users');
        }
        return view('admin/updateUser', [
            'user' => $user,
            'action' => 'edit'
        ]);
    }

    public function updateUser(Request $request): Json
    {
        $data = array();
        $id = intval($request->param('id'));
        $data['email'] = $request->param('email');
        $data['tel'] = $request->param('tel');

        $data['admin'] = $request->param('admin');
        $data['password'] = $request->param('password');

        if (empty($data['email'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少邮箱'
            ]);
        }

        return $this->app->userService->updateUser($id, $data);
    }

    public function postnewdog(Request $request)
    {
        $postinformation = array();
        $postinformation['Name'] = $request->param('Name');
        $postinformation['Breed'] = $request->param('Breed');
        $postinformation['Age'] = $request->param('Age');
        $postinformation['Gender'] = $request->param('Gender');
        $postinformation['Color'] = $request->param('Color');
        $postinformation['Weight'] = $request->param('Weight');
        $postinformation['SpayedOrNeutered'] = $request->param('SpayedOrNeutered');
        $postinformation['VaccinationStatus'] = $request->param('VaccinationStatus');
        $postinformation['SpecialNeeds'] = $request->param('SpecialNeeds');
        $postinformation['AdoptionRestrictions'] = $request->param('AdoptionRestrictions');
        $postinformation['AdoptionsStatus'] = $request->param('AdoptionsStatus');
        $postfile = request()->file('image');
        $saveName = Filesystem::putFile( 'dogimg', $postfile);
        $postinformation['image'] = $saveName;
        return $this->app->ApplyService->dogtable($postinformation);




    }
    public function newDog(): View
    {
        return view('admin/Addnewdog');
    }

    public function videocalladmin():view
    {
        return view('admin/videocalladmin');
    }




}
