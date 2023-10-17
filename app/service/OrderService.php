<?php
declare (strict_types=1);

namespace app\service;

use app\model\Order;
use app\model\OrderAddress;
use Exception;
use think\facade\Db;
use think\Paginator;
use think\response\Json;
use think\Service;

class OrderService extends Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('orderService', OrderService::class);
    }

    public function getOrdersCount(int $userID): int
    {
        $order = new Order();
        return $order->where('userID', $userID)->count();
    }

    public function getOrders(int $userID): Paginator
    {
        if ($userID == 0) {
            return Db::name('orders')
                ->alias('o')
                ->join('order_address oa', 'o.orderAddressID = oa.ID')
                ->join('channel c', 'o.channelID = c.ID')
                ->join('parcel p', 'o.ID = p.orderID', 'left')
                ->join('user u', 'o.userID = u.ID', 'left')
                ->field('o.*, o.uid as orderUID, oa.*, c.name as channelName, count(p.ID) as parcelCount, u.username as username')
                ->group('o.ID')
                ->order('o.createAt')
                ->paginate([
                    'list_rows' => 10,
                    'var_page' => 'page',
                ]);
        }

        return Db::name('orders')
            ->alias('o')
            ->join('order_address oa', 'o.orderAddressID = oa.ID')
            ->join('channel c', 'o.channelID = c.ID')
            ->field('o.*, o.uid as orderUID, oa.*, c.name as channelName')
            ->where('o.userID', $userID)
            ->order('o.createAt')
            ->paginate([
                'list_rows' => 10,
                'var_page' => 'page',
            ]);
    }

    public function deleteOrder(string $uid): Json
    {
        if (Db::name('orders')->where('uid', $uid)->delete()) {
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

    public function updateTrackNum(string $uid, string $trackNum, int $status): Json
    {
        if (Db::name('orders')->where('uid', $uid)->update(['trackNum' => $trackNum])) {
            if ($status == 0) {
                Db:
                name('orders')->where('uid', $uid)->update(['trackNum' => null]);
            }
            return $this->updateOrderStatus($uid, $status);
        } // 不能输入相同的快递单号
        elseif (Db::name('orders')->where('uid', $uid)->value('trackNum') == $trackNum) {
            return json([
                'ret' => 0,
                'msg' => '请勿输入相同的快递单号！'
            ]);
        } else {
            return json([
                'ret' => 0,
                'msg' => '更新失败'
            ]);
        }
    }

    public function updateOrderStatus(string $uid, int $status): Json
    {
        switch ($status) {
            case 1:
            {
                $time = date("Y-m-d H:i:s");
                break;
            }
            case 0:
            {
                $time = null;
                break;
            }
            default:
            {
                return json([
                    'ret' => 0,
                    'msg' => '更新失败'
                ]);
            }
        }
        if (Db::name('orders')->where('uid', $uid)->update(['dispatchStatus' => $status, 'dispatchTime' => $time])) {
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

    public function updatePaymentStatus(string $uid, int $status): Json
    {
        $order = new Order();
        $order = $order->where('uid', $uid)->findOrEmpty();
        if ($order->isEmpty()) {
            return json([
                'ret' => 0,
                'msg' => '订单不存在'
            ]);
        }
        $order->paymentStatus = $status;
        if ($status == 1) {
            $pointGain = $order->price * getSetting('pointRate');
            $this->app->userService->addPoints($order->userID, $pointGain);
        }
        if ($order->save()) {
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

    public function handleOrder(int $orderID, string $orderWeight, string $orderPrice): bool
    {
        $order = new Order();
        $order = $order->where('ID', $orderID)->findOrEmpty();
        if ($order->isEmpty()) {
            return false;
        }
        if ($order->payWithPoint) {
            $userPoints = $this->app->userService->getPoints($order->userID);
            $order->price = $orderPrice - $userPoints;
            $this->app->userService->emptyPoints($order->userID);
        } else {
            $order->price = $orderPrice;
        }
        $order->weight = $orderWeight;
        $order->processStatus = 1;
        return $order->save();
    }

    public function addOrder(int $userID, array $addressData, int $channelID, string $comment, bool $payWithPoint): bool
    {
        $order = new Order();
        $order->uid = $this->generateUid($userID);
        $order->userID = $userID;
        $order->orderAddressID = $this->addOrderAddress($addressData);
        $this->app->addressService->addAddress($userID, $addressData);
        $order->channelID = $channelID;
        $order->comment = $comment;
        $order->dispatchStatus = 0;
        $order->paymentStatus = 0;
        $order->processStatus = 0;
        $order->payWithPoint = $payWithPoint;
        $order->createAt = date('Y-m-d H:i:s');
        return $order->save();
    }

    public function generateUid(int $userID, int $retry=0): string
    {
        if ($retry>3) {
            throw new Exception('订单号生成失败');
        }
        // 将用户ID转换为4位数字
        $userID = sprintf("%04d", $userID);
        $randint = rand(1000, 9999);
        $date = date('Ymd');
        $uid = $date . $userID . $randint;
        // 检查重复
        $order = new Order();
        $order = $order->where('uid', $uid)->findOrEmpty();
        if ($order->isEmpty()) {
            return $uid;
        } else {
            return $this->generateUid((int)$userID, $retry+1);
        }
    }

    public function addOrderAddress(array $addressData): int
    {
        $orderAddress = new OrderAddress();
        $orderAddress = $orderAddress
            ->field('id')
            ->where('line1', $addressData['line1'])
            ->where('line2', $addressData['line2'])
            ->where('line3', $addressData['line3'])
            ->where('city', $addressData['city'])
            ->where('postcode', $addressData['postcode'])
            ->where('country', $addressData['country'])
            ->where('receiver', $addressData['receiver'])
            ->where('tel', $addressData['tel'])
            ->findOrEmpty();
        if ($orderAddress->isEmpty()) {
            $orderAddress = new OrderAddress();
            $orderAddress = $orderAddress->create($addressData);
        }
        return $orderAddress->id;
    }

    public function getUnprocessedOrderNum(int $userID): int
    {
        if ($userID == 0) {
            return Db::name('orders')->where('dispatchStatus', 0)->count();
        } else {
            return Db::name('orders')->where('dispatchStatus', 0)->where('userID', $userID)->count();
        }
    }

    public function getOrderParcels(int $userID, string $orderUID)
    {
        return Db::name('parcel')
            ->join('orders', 'parcel.orderID = orders.ID')
            ->where('orders.uid', $orderUID)
            ->where('parcel.userID', $userID)
            ->field('parcel.*, parcel.trackNum as trackNum, parcel.comment as comment')
            ->paginate(25);
    }

    public function deleteOrderImage(int $orderID): void
    {
        $images = Db::name('order_images')->where('orderID', $orderID)->field(['md5', 'ext'])->select();
        Db::name('order_images')->where('orderID', $orderID)->delete(); // TODO 定时任务回收文件？
        foreach ($images as $image) {
            $md5 = $image['md5'];
            $ext = $image['ext'];
            $prefix = substr($md5, 0, 2);
            // 剩余部分
            $md5 = substr($md5, 2);
            $filepath = app()->getRootPath() . "storage/order_images/$prefix/$md5." . $ext;
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }

    public function search(array $data, bool $paginate)
    {
        $order = new Order();
        $order = $order->alias('o')
            ->join('order_address oa', 'o.orderAddressID = oa.ID')
            ->join('channel c', 'o.channelID = c.ID')
            ->join('user u', 'o.userID = u.ID', 'left')
            ->field('o.*, o.id as orderID, oa.*, c.name as channelName, u.username as username')
            ->order('o.createAt');
        if (isset($data['orderUID'])) {
            $order = $order->where('o.uid', $data['orderUID']);
        }
        if (isset($data['username'])) {
            $order = $order->where('u.username', $data['username']);
        }
        if (isset($data['tel'])) {
            $order = $order->where('oa.tel', $data['tel']);
        }
        if (isset($data['dispatchStatus'])) {
            $order = $order->where('o.dispatchStatus', $data['dispatchStatus']);
        }
        if (isset($data['paymentStatus'])) {
            $order = $order->where('o.paymentStatus', $data['paymentStatus']);
        }
        if (isset($data['processStatus'])) {
            $order = $order->where('o.processStatus', $data['processStatus']);
        }
        if (isset($data['userID'])) {
            $order = $order->where('o.userID', $data['userID']);
        }
        if ($paginate) {
            return $order->paginate([
                'list_rows' => 20,
                'var_page' => 'page',
            ]);
        } else {
            return $order->select();
        }
    }
}
