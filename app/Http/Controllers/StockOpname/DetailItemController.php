<?php

namespace App\Http\Controllers\StockOpname;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\ItemStockOpname;
use Illuminate\Support\Facades\DB;

class DetailItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listitem($bulan, $tahun)
    {
        $data=ItemStockOpname::where([['periode',$bulan],['year',$tahun]])
        ->select(
            'itemno',
            'itemdesc',
            'unit',
            'qty',
            'id_wh',
            'warehouse',
            'stockop',
            'desc',
            )
        ->orderBy('stockop','ASC')->paginate(10);
        if($data){
            return response()->json([
                'success' => true,
                'message' =>'List Data Item',
                'data'    => $data
            ], 200);
        }else{
            return response()->json([
                'success' => true,
                'message' =>'Tidak Ada Data Stock Opname di bulan ini',
                'data'    => $data
            ], 204);
        }
    }

    public function detailitem($bulan, $tahun, $itemno)
    {
        $data=ItemStockOpname::where([['periode',$bulan], ['year',$tahun], ['itemno',$itemno]])
        ->select(
            'itemno',
            'itemdesc',
            'unit',
            'qty',
            'id_wh',
            'warehouse',
            'stockop',
            'desc',
            )->first();
        if($data){
            return response()->json([
                'success' => true,
                'message' =>'Detail Data Item',
                'data'    => $data
            ], 200);
        }else{
            return response()->json([
                'success' => true,
                'message' =>'Tidak Ada item ini',
                'data'    => $data
            ], 204);
        }
    }

    public function updatestockitem(Request $request)
    {
        $this->validate($request, [
            'itemno' => 'min:1|required',
            'periode','year','qty_real'=>'required',
        ]);

        $data=ItemStockOpname::where([['periode',$request->periode],['year',$request->year],['itemno',$request->itemno]])->update([
            'stockop'    => $request->qty_real,
            'desc'       => $request->desc,
            // 'post_by'    => Auth::user()->id,
            'updated_at' => Carbon::now()
        ]);
        if($data){
            return response()->json([
                'success' => true,
                'message' =>'Berhasil memperbarui data item ini',
            ], 200);
        }else{
            return response()->json([
                'success' => true,
                'message' =>'Gagal memperbarui data ini',
            ], 204);
        }
    }


}
