<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Address;
use app\model\Channel;
use app\model\Order;
use app\model\Parcel;
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
    public function addParcel(Request $request): Json
    {
        $method = $request->param('method'); // single/batch
        if ($method == 'single') {
            $trackNum = $request->param('trackNum');
            $comment = $request->param('comment');
            if (empty($trackNum)) {
                return json([
                    'ret' => 0,
                    'msg' => '缺少快递单号'
                ]);
            }
            if ($this->app->parcelService->addParcel(Session::get("userID"), $trackNum, $comment)) {
                return json([
                    'ret' => 1,
                    'msg' => '添加成功'
                ]);
            } else {
                return json([
                    'ret' => 0,
                    'msg' => '添加失败'
                ]);
            }
        } elseif ($method == 'batch') {
            $data = $request->param('data');
            if (empty($data)) {
                return json([
                    'ret' => 0,
                    'msg' => '缺少数据'
                ]);
            }
            // 格式： 单号-备注，其中备注可以为空
            // 检查内容是否合格，至少包含数字
            if (!preg_match("/\d+/", $data)) {
                return json([
                    'ret' => 0,
                    'msg' => '数据格式错误'
                ]);
            }
            if ($this->app->parcelService->processMultipleParcels(Session::get("userID"), $data)) {
                return json([
                    'ret' => 1,
                    'msg' => '添加成功'
                ]);
            } else {
                return json([
                    'ret' => 0,
                    'msg' => '添加失败'
                ]);
            }
        } else {
            return json([
                'ret' => 0,
                'msg' => '添加单号模式错误'
            ]);
        }
    }

    public function parcels(): View
    {
        $userID = Session::get("userID");
        $parcels = $this->app->parcelService->getParcels($userID);
        return view('/user/parcels', [
            'parcels' => $parcels
        ]);
    }

    public function index(): View
    {
        $userID = Session::get("userID");

        return view('/user/index', [
        ]);
    }

    public function logout(): Redirect
    {
        // 清除token
        $token = Cookie::get("token");
        if ($token) {
            Db::name('user_token')->where('token', $token)->delete();
        }
        // 清除session
        Session::delete("userID");
        // 清除cookie
        Cookie::delete("email");
        Cookie::delete("token");
        // 重定向到网站首页
        return redirect('/');
    }

    public function selectAddress(): View
    {
        $userID = Session::get("userID");
        $addresses = $this->app->addressService->getAddresses($userID);
        return view('/user/selectAddress', [
            'addresses' => $addresses
        ]);
    }

    public function addOrderPage(): View
    {
        $userID = Session::get("userID");
        $receivedParcels = $this->app->parcelService->getReceivedParcels($userID);
        $channels = $this->app->channelService->getChannels();
        $addressID = (int)$this->request->param('addressId');
        $address = new Address();
        if ($addressID != null) {
            $address = $this->app->addressService->getAddress($userID, $addressID);
            if (!$address) {
                return view('/error', ['msg' => '地址不存在']);
            }
        } else {
            $address->country = 'United Kingdom';
        }
        $points = $this->app->userService->getPoints($userID);
        $minPoints = getSetting('minUsablePoint');
        return view('/user/addOrder', [
            'receivedParcels' => $receivedParcels,
            'channels' => $channels,
            'address' => $address,
            'points' => $points,
            'minPoints' => $minPoints
        ]);
    }

    public function orders(): View
    {
        $userID = Session::get("userID");
        $orders = $this->app->orderService->getOrders($userID);
        return view('/user/orders', [
            'orders' => $orders
        ]);
    }

    public function addOrder(Request $request): Json
    {
        $userID = Session::get("userID");
        $addressData = array();
        $addressData['receiver'] = $request->param('receiver');
        $addressData['tel'] = $request->param('tel');
        $addressData['line1'] = $request->param('line1');
        $addressData['line2'] = $request->param('line2');
        $addressData['line3'] = $request->param('line3');
        $addressData['city'] = $request->param('city');
        $addressData['postcode'] = $request->param('postcode');
        $addressData['country'] = $request->param('country');
        $channelID = (int)$request->param('channel');
        $comment = $request->param('comments');
        $payWithPoint = $request->param('payWithPoint');
        $selectedParcels = $request->param('selectedParcelIds');

        if (empty($addressData['receiver'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少收件人'
            ]);
        }

        if (empty($addressData['tel'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少电话'
            ]);
        }

        if (empty($addressData['line1'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少主要地址'
            ]);
        }

        if (empty($addressData['city'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少城市'
            ]);
        }

        if (empty($addressData['postcode'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少邮编'
            ]);
        }

        if (empty($addressData['country'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少国家'
            ]);
        }

        if (empty($channelID)) {
            return json([
                'ret' => 0,
                'msg' => '缺少渠道'
            ]);
        }

        if (empty($selectedParcels) || in_array(null, $selectedParcels)) {
            return json([
                'ret' => 0,
                'msg' => '缺少包裹'
            ]);
        }

        // 检查包裹是否属于当前用户
        foreach ($selectedParcels as $selectedParcel) {
            $parcel = new Parcel();
            $parcel = $parcel->where('id', $selectedParcel)->findOrEmpty();
            if ($parcel->isEmpty()) {
                return json([
                    'ret' => 0,
                    'msg' => '有包裹不存在'
                ]);
            }
            if ($parcel['userID'] != $userID) {
                return json([
                    'ret' => 0,
                    'msg' => '有包裹不属于当前用户'
                ]);
            }
        }

        // 检查渠道是否存在
        $channel = new Channel();
        $channel = $channel->where('id', $channelID)->findOrEmpty();
        if ($channel->isEmpty()) {
            return json([
                'ret' => 0,
                'msg' => '渠道不存在'
            ]);
        }

        // 检查积分是否足够
        $points = $this->app->userService->getPoints($userID);
        $minPoints = getSetting('minUsablePoint');
        if ($payWithPoint && ($points < $minPoints)) {
            return json([
                'ret' => 0,
                'msg' => '积分不足'
            ]);
        }

        if ($this->app->orderService->addOrder($userID, $addressData, $channelID, $comment, $payWithPoint)) {
            $orderID = Db::name('orders')->where('userID', $userID)->max('id');
            foreach ($selectedParcels as $selectedParcel) {
                Db::name('parcel')->where('id', $selectedParcel)->update(['orderID' => $orderID, 'status' => 'PROCESSED']);
            }
            return json([
                'ret' => 1,
                'msg' => '添加成功'
            ]);
        } else {
            return json([
                'ret' => 0,
                'msg' => '添加失败'
            ]);
        }

    }

    public function settings(): View
    {
        $userID = Session::get("userID");
        $user = $this->app->userService->getUser($userID);
        return view('/user/settings', [
            'user' => $user
        ]);
    }

    public function updateSettings(): Json
    {
        $userID = Session::get("userID");
        $username = $this->request->param('username');
        $email = $this->request->param('email');
        $password = $this->request->param('password');
        $tel = $this->request->param('tel');
        return $this->app->userService->updateUser($userID, [
            'username' => $username,
            'password' => $password,
            'tel' => $tel,
            'email' => $email
        ]);
    }

    public function channels(): View
    {
        $list = $this->app->channelService->getChannels(true);
        return view('/user/channels', [
            'channels' => $list
        ]);
    }

    public function addresses(): View
    {
        $userID = Session::get("userID");
        $addresses = $this->app->addressService->getAddresses($userID);
        return view('/user/addresses', [
            'addresses' => $addresses
        ]);
    }

    public function addAddress(Request $request): Json
    {
        $addressData = array();
        $userID = Session::get("userID");
        $addressData['receiver'] = $request->param('receiver');
        $addressData['tel'] = $request->param('tel');
        $addressData['line1'] = $request->param('line1');
        $addressData['line2'] = $request->param('line2');
        $addressData['line3'] = $request->param('line3');
        $addressData['city'] = $request->param('city');
        $addressData['postcode'] = $request->param('postcode');
        $addressData['country'] = $request->param('country');
        if (empty($addressData['receiver'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少收件人'
            ]);
        }
        if (empty($addressData['tel'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少电话'
            ]);
        }
        if (empty($addressData['line1'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少详细地址'
            ]);
        }
        if (empty($addressData['city'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少城市'
            ]);
        }
        if (empty($addressData['postcode'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少邮编'
            ]);
        }
        if (empty($addressData['country'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少国家'
            ]);
        }
        return $this->app->addressService->addAddress($userID, $addressData);
    }

    public function deleteAddress(string $id): Json
    {
        $userID = Session::get("userID");
        if (empty($id)) {
            return json([
                'ret' => 0,
                'msg' => '缺少地址ID'
            ]);
        }
        // 转为int
        $addressID = intval($id);
        if ($this->app->addressService->deleteAddress($userID, $addressID)) {
            return json([
                'ret' => 1,
                'msg' => '删除成功'
            ]);
        } else {
            return json([
                'ret' => 0,
                'msg' => '删除失败'
            ]);
        }
    }

    public function addAddressPage(): View
    {
        $address = new Address();
        $address->id = 0;
        $address->country = 'United Kingdom';
        return view('/user/addAddress', [
            'action' => 'add',
            'address' => $address
        ]);
    }

    public function editAddress(string $id)
    {
        $userID = Session::get("userID");
        if (empty($id)) {
            return json([
                'ret' => 0,
                'msg' => '缺少地址ID'
            ]);
        }
        // 转为int
        $addressID = intval($id);
        $address = $this->app->addressService->getAddress($userID, $addressID);
        if (empty($address)) {
            return view('/error', ['msg' => '地址不存在']);
        } else {
            return view('/user/addAddress', [
                'address' => $address,
                'action' => 'edit'
            ]);
        }
    }

    public function updateAddress(Request $request): Json
    {
        $addressID = $request->param('addressID');
        $data = array();
        $data['userID'] = Session::get("userID");
        if (empty($addressID)) {
            return json([
                'ret' => 0,
                'msg' => '缺少地址ID'
            ]);
        }
        $data['receiver'] = $request->param('receiver');
        $data['tel'] = $request->param('tel');
        $data['line1'] = $request->param('line1');
        $data['line2'] = $request->param('line2');
        $data['line3'] = $request->param('line3');
        $data['city'] = $request->param('city');
        $data['postcode'] = $request->param('postcode');
        $data['country'] = $request->param('country');
        if (empty($data['receiver'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少收件人'
            ]);
        }
        if (empty($data['tel'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少电话'
            ]);
        }
        if (empty($data['line1'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少详细地址'
            ]);
        }
        if (empty($data['city'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少城市'
            ]);
        }
        if (empty($data['postcode'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少邮编'
            ]);
        }
        if (empty($data['country'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少国家'
            ]);
        }
        $address = new Address();
        $address = $address->where('id', $addressID)->where('userID', $data['userID'])->find();
        if (empty($address)) {
            return json([
                'ret' => 0,
                'msg' => '地址不存在'
            ]);
        }
        if ($this->app->addressService->updateAddress($addressID, $data)) {
            return json([
                'ret' => 1,
                'msg' => '更新成功'
            ]);
        } else {
            return json([
                'ret' => 0,
                'msg' => '更新失败'
            ]);
        }
    }

    public function getOrderImagePage(string $orderUID): View
    {
        $userID = Session::get("userID");
        if (empty($orderUID)) {
            return view('/error', ['msg' => '缺少订单编号']);
        }
        // 判断订单是否属于该用户
        $order = new Order();
        $order = $order->where('uid', $orderUID)->where('userID', $userID)->findOrEmpty();
        if ($order->isEmpty()) {
            return view('/error', ['msg' => '订单不存在']);
        }
        $orderID = $order['id'];
        $images = Db::name('order_images')->where('orderID', $orderID)->order('id', 'asc')->field('md5')->select();
        if (count($images) == 0) {
            return view('/error', ['msg' => '订单还没有图片']);
        }
        return view('user/orderImage', [
            'images' => $images
        ]);
    }

    public function getOrderImage(string $md5): ?File
    {
        $userID = Session::get("userID");
        if (empty($md5)) {
            return null;
        }
        // 查找图片是否存在
        $image = Db::name('order_images')->join('orders', 'orders.id = order_images.orderID')->where('md5', $md5)->where('userID', $userID)->field('order_images.ext')->find();
        if (empty($image)) {
            return null;
        }
        // 检查文件是否存在
        // 获取前两位
        $prefix = substr($md5, 0, 2);
        // 剩余部分
        $md5 = substr($md5, 2);
        $filepath = app()->getRootPath() . "storage/order_images/$prefix/$md5." . $image['ext'];
        if (!file_exists($filepath)) {
            return null;
        } else {
            $file = new File($filepath);
            return $file->mimeType("image/{$image['ext']}")->force(false);
        }
    }

    public function getOrderParcels(string $orderUID): View
    {
        $userID = Session::get("userID");
        if (empty($orderUID)) {
            return view('/error', ['msg' => '缺少订单编号']);
        }
        $parcels = $this->app->orderService->getOrderParcels((int)$userID, $orderUID);
        if ($parcels->isEmpty()) {
            return view('/error', ['msg' => '订单不存在']);
        } else {
            return view('/user/parcels', [
                'parcels' => $parcels
            ]);
        }
    }

    public function paymentPage(string $id): View
    {
        $userID = Session::get("userID");
        if (empty($id)) {
            return view('/error', ['msg' => '缺少订单编号']);
        }
        $order = new Order();
        $order = $order->where('uid', $id)->where('userID', $userID)->findOrEmpty();
        if ($order->isEmpty()) {
            return view('/error', ['msg' => '订单不存在']);
        }
        if ($order->paymentStatus == 1) {
            return view('/error', ['msg' => '订单已支付']);
        }
        if ($order->processStatus == 0) {
            return view('/error', ['msg' => '订单未处理']);
        }
        return view('/user/payment', [
            'order' => $order
        ]);
    }
}
