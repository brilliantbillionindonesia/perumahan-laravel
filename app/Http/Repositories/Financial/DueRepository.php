<?php

namespace App\Http\Repositories\Financial;

use App\Constants\DueStatusOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DueRepository
{
    public static function duesSummary(Request $request)
    {
        $housingId = $request->input('housing_id');
        $periodeLike = $request->input('periode');
        $houseId = $request->input('house_id');
        $status = $request->input('status');
        $statusArr = [];
        if ($status == 'unpaid') {
            $statusArr = DueStatusOption::NOTPAID;
        }
        if ($status == 'paid') {
            $statusArr = [DueStatusOption::PAID];
        }

        // Subquery CTE: ranked
        $ranked = DB::table('dues as d')
            ->selectRaw("
                d.*,
                ROW_NUMBER() OVER (
                    PARTITION BY d.housing_id, d.house_id, d.fee_id, d.periode, d.status
                    ORDER BY d.created_at DESC
                ) as rn
            ")
            ->where('d.housing_id', $housingId)
            ->when($periodeLike, function ($q) use ($periodeLike) {
                $q->where('d.periode', 'like', $periodeLike . '%');
            })
            ->when($houseId, function ($q) use ($houseId) {
                $q->where('d.house_id', $houseId);
            })
            ->when($status, function ($q) use ($statusArr) {
                $q->whereIn('d.status', $statusArr);
            });

        $result = DB::query()
            ->fromSub($ranked, 'r')
            ->join('houses as h', 'h.id', '=', 'r.house_id')
            ->join('citizens as c', 'c.id', '=', 'h.head_citizen_id')
            ->where('r.rn', 1)
            ->groupBy('r.house_id', 'h.house_name', 'h.block', 'h.number', 'r.periode')
            ->orderBy('r.periode')
            ->orderBy('h.block')
            ->orderBy('h.number')
            ->selectRaw("
                r.house_id,
                h.house_name,
                h.block,
                h.number,
                CONCAT(h.block, '/', h.number) as block_number,
                r.periode,
                MAX(c.fullname)  as head_fullname,
                COUNT(r.id)      as total_dues,
                SUM(r.amount)    as total_amount,
                SUM(CASE WHEN LOWER(r.status) = 'paid' THEN 1 ELSE 0 END)              AS total_paid,
                SUM(CASE WHEN COALESCE(LOWER(r.status), '') <> 'paid' THEN 1 ELSE 0 END) AS total_unpaid,
                COALESCE(SUM(CASE WHEN LOWER(r.status) = 'paid' THEN r.amount END), 0) AS amount_paid,
                COALESCE(SUM(CASE WHEN COALESCE(LOWER(r.status), '') <> 'paid' THEN r.amount END), 0) AS amount_unpaid
            ");

        return $result;
    }

    public static function showDetailQuery(Request $request)
    {

        $housingId = $request->input('housing_id');
        $houseId = $request->input('house_id');
        $status = $request->input('status');
        $statusArr = [];
        if ($status == 'unpaid') {
            $statusArr = DueStatusOption::NOTPAID;
        }
        if ($status == 'paid') {
            $statusArr = [DueStatusOption::PAID];
        }

        $result = DB::table('dues as d')
            ->join('fees as f', 'f.id', '=', 'd.fee_id')
            ->where('d.housing_id', $housingId)
            ->where('d.house_id', $houseId)
            ->when($status, function ($q) use ($statusArr) {
                $q->whereIn('status', $statusArr);
            })
            ->select(
                'd.id as due_id',
                'd.amount',
                'd.periode',
                'f.name',
                'd.status'
            );

        return $result;
    }
}
