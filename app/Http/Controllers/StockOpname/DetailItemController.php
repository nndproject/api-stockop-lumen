<?php

namespace App\Http\Controllers\StockOpname;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\ItemStockOpname;
use App\Models\StockOpname;
use App\Models\FilesStockOpname;
use Illuminate\Support\Facades\DB;
use Auth;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class DetailItemController extends Controller
{
    private $periode;
    private $year;

    public function __construct()
    {
        $this->periode = Carbon::now()->submonth()->format('F');
        $this->year = Carbon::now()->format('Y');
        // $this->middleware('auth');
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
            ], 404);
        }
    }

    public function detailitem($bulan, $tahun, $itemno)
    {
        $data=ItemStockOpname::where([['periode',$bulan], ['year',$tahun], ['itemno',$itemno]])->first();
        if($data){
            return response()->json([
                'success' => true,
                'message' =>'Detail Data Item',
                'data'    => array([
                    'itemno'          => $data->itemno,
                    'itemdesc'        => $data->itemdesc,
                    'qty'             => $data->qty,
                    'unit'            => $data->unit,
                    'stockop'         => $data->stockop,
                    'location'        => $data->warehouse,
                    'status'          => fdatastatus($data),
                    'message'         => $data->desc,
                    'updated_at'      => Carbon::parse($data->updated_at)->format('Y-m-d H:i'),
                    'posted_by'       => ($data->users) ? $data->users->name : null,

                ])
            ], 200);
        }else{
            return response()->json([
                'success' => true,
                'message' =>'Tidak Ada item ini',
                'data'    => $data
            ], 404);
        }
    }

    public function updatestockitem(Request $request)
    {
        $this->validate($request, [
            'itemno' => 'min:1|required',
            'stockop'=>'required',
        ]);

        $stockop    = StockOpname::where([['periode', "May"],['year', $this->year]])->first();
        if($stockop)
        {
            $data       = ItemStockOpname::where([['periode', "May"],['year', $this->year],['itemno', $request->itemno]])->update([
                'stockop'    => $request->stockop,
                'desc'       => ($request->desc) ?? "-",
                // 'post_by'    => Auth::user()->id,
                'updated_at' => Carbon::now()
            ]);
    
            if($request->hasFile('img')){
    
                $tempname = saveAndResizeImage($request->file('img'), "stock-opname", $this->year.'/'.strtolower("May"), 800, 600 );
                $files = new FilesStockOpname();
                $files->itemno      = $request->itemno;
                $files->id_stockop  = $stockop->id;
                $files->files       = $tempname;
                $files->save();
            }
        }
        
        
        if($data){
            return response()->json([
                'success' => true,
                'message' =>'Berhasil memperbarui data item ini',
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' =>'Gagal memperbarui data ini',
            ], 404);
        }
    }


}
