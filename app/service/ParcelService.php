<?php
declare (strict_types=1);

namespace app\service;

use app\model\Parcel;
use think\facade\Db;
use think\Paginator;
use think\response\Json;
use think\Service;

class ParcelService extends Service
{
    public function register(): void
    {
        $this->app->bind('parcelService', ParcelService::class);
    }

    public function deleteParcel(int $id): Json
    {
        if (Db::name('parcel')->where('ID', $id)->delete()) {
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

    public function updateParcelStatus(int $id, string $status): Json
    {
        switch ($status) {
            case "RECEIVED":
            {
                $time = date("Y-m-d H:i:s");
                break;
            }
            case "NOTRECEIVED":
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
        if (Db::name('parcel')->where('ID', $id)->update(['status' => $status, 'receivedAt' => $time])) {
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

    public function processMultipleParcels(int $userID, string $data): bool
    {
        // 格式： 单号--备注
        $parcels = explode("\n", $data);
        foreach ($parcels as $parcel) {
            $parcel = explode("-", $parcel);
            if (count($parcel) != 2) {
                $comment = "";
            } else {
                $comment = $parcel[1];
            }
            $trackNum = $parcel[0];
            if ($this->addParcel($userID, $trackNum, $comment)) {
                continue;
            } else {
                return false;
            }
        }
        return true;
    }

    public function addParcel(int $userID, string $trackNum, string $comment): bool
    {
        $parcel = new Parcel();
        // 检查单号是否已存在
        $parcel = $parcel->where('trackNum', $trackNum)->findOrEmpty();
        if ($parcel->isEmpty()) {
            $parcel->trackNum = $trackNum;
            $parcel->status = 'NOTRECEIVED';
            $parcel->createdAt = date("Y-m-d H:i:s");
        }
        $parcel->userID = $userID;
        $parcel->comment = $comment;
        return $parcel->save();
    }

    public function getParcels(int $userID): Paginator
    {
        if ($userID == 0) {
            return Db::name('parcel')
                ->alias('p')
                ->leftjoin('user u', 'p.userID = u.ID')
                ->field('p.*, u.username')
                ->whereIn('p.status', ['NOTRECEIVED', 'RECEIVED'])
                ->order('p.createdAt', 'desc')->paginate(25);

        } else {
            return Db::name('parcel')->where('userID', $userID)->whereIn('status', ['NOTRECEIVED', 'RECEIVED'])->order('createdAt', 'desc')->paginate(25);
        }
    }

    public function getReceivedParcels(int $userID): Paginator
    {
        if ($userID == 0) {
            return Db::name('parcel')
                ->alias('p')
                ->join('user u', 'p.userID = u.ID')
                ->field('p.*, u.username')
                ->where('p.status', 'RECEIVED')
                ->order('p.createdAt', 'desc')->paginate(25);

        } else {
            return Db::name('parcel')->where('userID', $userID)->where('status', 'RECEIVED')->where('orderID', null)->order('createdAt', 'desc')->paginate(25);
        }
    }

    public function getUnreceivedParcelsCount(int $userID): int
    {
        if ($userID == 0) {
            return Db::name('parcel')->where('status', 'NOTRECEIVED')->count();
        } else {
            return Db::name('parcel')->where('userID', $userID)->where('status', 'NOTRECEIVED')->count();
        }
    }

    public function getReceivedParcelsCount(int $userID): int
    {
        if ($userID == 0) {
            return Db::name('parcel')->where('status', 'RECEIVED')->count();
        } else {
            return Db::name('parcel')->where('userID', $userID)->where('status', 'RECEIVED')->count();
        }
    }

    public function batchRecord(string $data): Json
    {
        $parcels = explode("\n", $data);
        foreach ($parcels as $parcel) {
            $thisParcel = new Parcel();
            $thisParcel = $thisParcel->where('trackNum', $parcel)->findOrEmpty();
            if (!$thisParcel->isEmpty()) {
                // 快递单号已存在
                $thisParcel->status = 'RECEIVED';
                $thisParcel->receivedAt = date("Y-m-d H:i:s");
                $thisParcel->save();
            } else {
                // 快递单号不存在
                $thisParcel = new Parcel();
                $thisParcel->trackNum = $parcel;
                $thisParcel->status = 'RECEIVED';
                $thisParcel->receivedAt = date("Y-m-d H:i:s");
                $thisParcel->createdAt = date("Y-m-d H:i:s");
                $thisParcel->save();
            }
        }
        return json([
            'ret' => 1,
            'msg' => '批量录入成功'
        ]);
    }
}
