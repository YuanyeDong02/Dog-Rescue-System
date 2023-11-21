<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;

use think\facade\Filesystem;
use think\Request;
use think\response\Json;
use think\response\View;

class AdminController extends BaseController
{
    public function index()
    {
        return view('admin/index');
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







}
