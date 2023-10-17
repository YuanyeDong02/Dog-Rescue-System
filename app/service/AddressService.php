<?php
declare (strict_types=1);

namespace app\service;

use app\model\Address;
use think\facade\Db;
use think\Paginator;
use think\response\Json;
use think\Service;

class AddressService extends Service
{
    public function register()
    {
        $this->app->bind('addressService', AddressService::class);
    }

    public function addAddress(int $userID, $addressData): Json
    {
        // 检查是否重复
        $address = new Address();
        $address = $address
            ->where('userID', $userID)
            ->where('receiver', $addressData['receiver'])
            ->where('line1', $addressData['line1'])
            ->where('line2', $addressData['line2'])
            ->where('line3', $addressData['line3'])
            ->where('city', $addressData['city'])
            ->where('postcode', $addressData['postcode'])
            ->where('country', $addressData['country'])
            ->where('tel', $addressData['tel'])
            ->findOrEmpty();
        if ($address->isExists()) {
            return json([
                'ret' => 0,
                'msg' => '地址已存在',
            ]);
        } else {
            $address = new Address();
            $addressData['userID'] = $userID;
            $address->create($addressData);
            return json([
                'ret' => 1,
                'msg' => '地址添加成功',
            ]);
        }
    }

    public function deleteAddress(int $userID, int $id): bool
    {
        $address = new Address();
        $address = $address->where('id', $id)->find();
        if ($address->count() == 0) {
            return false;
        }
        if ($userID != 0 && $address->userID != $userID) {
            return false;
        } else {
            return $address->delete();
        }
    }

    public function updateAddress($addressID, $data): bool
    {
        $address = new Address();
        $address->where('id', $addressID)->update($data);
        return true;
    }

    public function getAddresses(int $userID): Paginator
    {
        if ($userID == 0) {
            return Db::name('address')->paginate(25);
        } else {
            return Db::name('address')->where('userID', $userID)->paginate(10);
        }
    }

    public function getAddress(int $userID, int $addressID): ?Address
    {
        $address = new Address();
        $address = $address->where('id', $addressID)->findOrEmpty();
        if (!$address->isExists() || ($userID != 0 && $address->userID != $userID)) {
            return null;
        } else {
            return $address;
        }
    }

}
