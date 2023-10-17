<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\Channel;
use app\model\Order;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Filesystem;
use think\Request;
use think\response\Json;
use think\response\View;

class AdminController extends BaseController
{
    public function index()
    {
        $notReceivedNum = $this->app->parcelService->getUnreceivedParcelsCount(0);
        $receivedNum = $this->app->parcelService->getReceivedParcelsCount(0);
        $unprocessedOrderNum = $this->app->orderService->getUnprocessedOrderNum(0);
        return view('admin/index', [
            'notReceivedNum' => $notReceivedNum,
            'receivedNum' => $receivedNum,
            'unprocessedOrderNum' => $unprocessedOrderNum
        ]);
    }

    public function parcels(): View
    {
        $parcels = $this->app->parcelService->getParcels(0);
        return view('admin/parcels', [
            'parcels' => $parcels
        ]);
    }

    public function batchParcel(): Json
    {
        $data = $this->request->param('data');
        return $this->app->parcelService->batchRecord($data);
    }

    public function parcelDelete(string $id): Json
    {
        return $this->app->parcelService->deleteParcel(intval($id));
    }

    public function parcelSetStatus(string $id): Json
    {
        $status = $this->request->param('status');
        return $this->app->parcelService->updateParcelStatus(intval($id), $status);
    }

    public function users(): View
    {
        $users = $this->app->userService->getUsers();
        return view('admin/users', [
            'users' => $users
        ]);
    }

    public function orders(Request $request): View
    {
        $data = array();
        $data['orderUID'] = $request->param('orderUID');
        $data['username'] = $request->param('username');
        $data['tel'] = $request->param('tel');
        $data['dispatchStatus'] = $request->param('dispatchStatus');
        $data['paymentStatus'] = $request->param('paymentStatus');
        $data['processStatus'] = $request->param('processStatus');
        $orders = $this->app->orderService->search($data, true);
        return view('admin/orders', [
            'orders' => $orders,
        ]);
    }

    public function orderDelete(string $uid): Json
    {
        return $this->app->orderService->deleteOrder($uid);
    }

    public function handleOrderPage(string $uid): View
    {
        $order = new Order();
        $order = $order->where('uid', $uid)->findOrEmpty();
        if ($order->isEmpty()) {
            return view('/error', [
                'msg' => '订单不存在'
            ]);
        }
        $orderDetail = Db::name('orders')
            ->alias('o')
            ->join('order_address oa', 'o.orderAddressID = oa.ID')
            ->join('channel c', 'o.channelID = c.ID')
            ->join('user u', 'o.userID = u.ID', 'left')
            ->field('o.*, o.id as orderID, oa.*, c.name as channelName, u.username as username,u.tel as tel, c.firstprice, c.laterprice')
            ->where('o.uid', $uid)->find();
        if ($orderDetail['payWithPoint']) {
            $userPoints = Db::name('user')->where('ID', $orderDetail['userID'])->value('points');
        } else {
            $userPoints = 0;
        }
        return view('admin/orderHandle', [
            'order' => $orderDetail,
            'userPoints' => $userPoints
        ]);

    }

    public function handleOrder(Request $request): Json
    {
        $orderID = $request->param('orderID');
        $orderWeight = $request->param('orderWeight');
        $orderPrice = $request->param('orderPrice');

        if (empty($orderID)) {
            return json([
                'ret' => 0,
                'msg' => '缺少订单号'
            ]);
        }

        if (empty($orderWeight)) {
            return json([
                'ret' => 0,
                'msg' => '缺少订单重量'
            ]);
        }

        if (empty($orderPrice)) {
            return json([
                'ret' => 0,
                'msg' => '缺少订单价格'
            ]);
        }
        $order = new Order();
        $order = $order->where('ID', intval($orderID))->findOrEmpty();
        if ($order->isEmpty()) {
            return json([
                'ret' => 0,
                'msg' => '订单不存在'
            ]);
        }
        $this->app->orderService->handleOrder(intval($orderID), $orderWeight, $orderPrice);
        $this->app->orderService->deleteOrderImage(intval($orderID));
        $files = request()->file('file');
        for ($i = 0; $i < count($files); $i++) {
            $file = $files[$i];
            try {
                validate([
                    'file' => [
                        'fileSize' => 1024 * 1024 * 8,
                        'fileExt' => 'png,jpg,jpeg'
                    ]
                ])->check(['file' => $file]);
                $md5 = $file->hash('md5');
                $ext = $file->getOriginalExtension();
                Filesystem::disk('public')->putFile("order_images", $file, "md5");
                Db::name('order_images')->insert([
                    'orderID' => intval($orderID),
                    'md5' => $md5,
                    'ext' => $ext,
                    'date' => date('Y-m-d H:i:s')
                ]);
            } catch (ValidateException $e) {
                return json([
                    'ret' => 0,
                    'msg' => $e->getMessage()
                ]);
            }
        }
        return json([
            'ret' => 1,
            'msg' => '已处理'
        ]);

    }

    public function orderSetStatus(string $uid): Json
    {
        $orderStatus = $this->request->param('orderStatus');
        $orderTrackNum = $this->request->param('trackingNumber');
        $paymentStatus = $this->request->param('paymentStatus');

        if ($paymentStatus !== null) {
            return $this->app->orderService->updatePaymentStatus($uid, $paymentStatus);
        }
        if ($orderTrackNum !== null) {
            return $this->app->orderService->updateTrackNum($uid, $orderTrackNum, $orderStatus);
        }

        return $this->app->orderService->updateOrderStatus($uid, $orderStatus);
    }

    public function setPaymentStatus(string $uid): Json
    {
        $status = $this->request->param('status');
        return $this->app->orderService->updatePaymentStatus($uid, $status);
    }

    public function deleteUser(string $id): Json
    {
        if ($this->app->userService->deleteUser(intval($id))) {
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
        $data['username'] = $request->param('username');
        $data['email'] = $request->param('email');
        $data['points'] = $request->param('points');
        $data['tel'] = $request->param('tel');
        $data['admin'] = $request->param('admin');
        $data['password'] = $request->param('password');
        if (empty($data['username'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少用户名'
            ]);
        }
        if (empty($data['email'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少邮箱'
            ]);
        }
        if (empty($data['points']) && $data['points'] != 0) {
            return json([
                'ret' => 0,
                'msg' => '缺少积分'
            ]);
        }
        if (empty($data['tel'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少电话'
            ]);
        }
        return $this->app->userService->updateUser($id, $data);
    }

    public function channels(): View
    {
        $channels = $this->app->channelService->getChannels(true);
        return view('/admin/channels', [
            'channels' => $channels
        ]);
    }

    public function channelsDetail(string $id): View
    {
        $channel = new Channel();
        $channel = $channel->where('id', intval($id))->findOrEmpty();
        if ($channel->isEmpty()) {
            return view('/error', [
                'msg' => '渠道不存在'
            ]);
        }
        return view('/admin/channelDetail', [
            'channel' => $channel,
            'action' => 'edit'
        ]);
    }

    public function addChannelPage(): View
    {
        $channel = new Channel();
        $channel->id = 0;
        return view('/admin/channelDetail', [
            'channel' => $channel,
            'action' => 'add'
        ]);
    }

    public function addChannel(Request $request): Json
    {
        $data = array();
        $data['name'] = $request->param('name');
        $data['effectiveness'] = $request->param('effectiveness');
        $data['firstPrice'] = $request->param('firstPrice');
        $data['laterPrice'] = $request->param('laterPrice');
        $data['comment'] = $request->param('comment');
        if (empty($data['name'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少渠道名称'
            ]);
        }
        if (empty($data['firstPrice'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少首重价格'
            ]);
        }
        if (empty($data['laterPrice'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少续重价格'
            ]);
        }
        if (empty($data['effectiveness'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少时效'
            ]);
        }
        $result = $this->app->channelService->addChannel($data);
        if ($result) {
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

    public function updateChannel(string $id, Request $request): Json
    {
        $data = array();
        $data['name'] = $request->param('name');
        $data['effectiveness'] = $request->param('effectiveness');
        $data['firstPrice'] = $request->param('firstPrice');
        $data['laterPrice'] = $request->param('laterPrice');
        $data['comment'] = $request->param('comment');
        if (empty($data['name'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少渠道名称'
            ]);
        }
        if (empty($data['firstPrice'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少首重价格'
            ]);
        }
        if (empty($data['laterPrice'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少续重价格'
            ]);
        }
        if (empty($data['effectiveness'])) {
            return json([
                'ret' => 0,
                'msg' => '缺少时效'
            ]);
        }
        $result = $this->app->channelService->updateChannel(intval($id), $data);
        if ($result) {
            return json([
                'ret' => 1,
                'msg' => '修改成功'
            ]);
        } else {
            return json([
                'ret' => 0,
                'msg' => '修改失败'
            ]);
        }
    }

    public function deleteChannel(string $id): Json
    {
        if ($this->app->channelService->deleteChannel(intval($id))) {
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

    public function settings(): View
    {
        $settings = $this->app->settingService->getSettings();
        return view('/admin/settings', [
            'settings' => $settings
        ]);
    }

    public function settingDetail(string $id): View
    {
        $setting = Db::name('setting')->where('id', intval($id))->find();
        if (!$setting) {
            return view('/error', [
                'msg' => '设置项不存在'
            ]);
        }
        return view('/admin/settingDetail', [
            'setting' => $setting
        ]);
    }

    public function updateSetting(Request $request): Json
    {
        $id = $request->param('id');
        $value = $request->param('value');
        if (empty($id)) {
            return json([
                'ret' => 0,
                'msg' => '缺少id'
            ]);
        }
        if (empty($value)) {
            return json([
                'ret' => 0,
                'msg' => '缺少value'
            ]);
        }
        $value = htmlspecialchars($value);
        $this->app->settingService->updateSetting((int)$id, $value);
        return json([
            'ret' => 1,
            'msg' => '修改成功'
        ]);
    }
}
