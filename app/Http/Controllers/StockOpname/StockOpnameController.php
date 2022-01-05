<?php

namespace App\Http\Controllers\StockOpname;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StockOpname;
use App\Models\ItemStockOpname;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockOpnameController extends Controller
{
    private $periode;
    private $year;

    public function __construct()
    {
        $this->periode = Carbon::now()->submonths(2)->format('F');
        $this->year = Carbon::now()->subyear()->format('Y');
        // $this->middleware('auth');
    }
    
    public function index()
    {
        // $data=StockOpname::where('status','FINISHED')->latest()->first();
        $data=StockOpname::where('status','WAITING')->latest()->first();
        // $data =  DB::table('stockop')->first();
        // $data = app('db')->select("SELECT * FROM stockop");
        if($data){
            return response()->json([
                'success' => true,
                'message' =>'List Data Stock Opname',
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

    public function dashboard()
    {
        $stockop    = StockOpname::where([['periode', $this->periode],['year', $this->year]])->first();
        if(isset($stockop)){
            $jumAll = ItemStockOpname::where([['periode',$stockop->periode],['year',$stockop->year]])->count();
            $jumAntrian = ItemStockOpname::where([['periode',$stockop->periode],['year',$stockop->year],['stockop', null]])->count();
            $jumStockAll = ItemStockOpname::where([['periode',$stockop->periode],['year',$stockop->year],['stockop','!=', null]])->count();

            return response()->json([
                'success'   => true,
                'message'   => 'Tidak ada data Stock Opname pada periode '.strtoupper($this->periode).' '.$this->year,
                'data'      => array([
                        'pengumuman' => 'Stok Opname  PT. Arion Indonesia periode '.strtoupper($this->periode).' '.$this->year
                                        .' akan dilaksanakan pada tanggal '.Carbon::now()->subMonth()->endOfMonth()->format('d F Y').' mulai pukul 10:00',
                        'all'       => (float) number_format($jumAll, 0, ',', '.'),
                        'antrian'   => (float) number_format($jumAntrian, 0, ',', '.'),
                        'selesai'   => (float) number_format($jumStockAll, 0, ',', '.'),
                        'progress'  => (float) number_format((($jumStockAll/$jumAll)*100), 2, ',', '.'), 
                ])
            ], 200); 

        }else{
            return response()->json([
                'success' => true,
                'message' =>'Tidak ada data Stock Opname pada periode '.strtoupper($this->periode).' '.$this->year,
                'data'      => array([
                    'pengumuman' => 'Stok Opname  PT. Arion Indonesia periode '.strtoupper($this->periode).' '.$this->year
                                    .' akan dilaksanakan pada tanggal '.Carbon::now()->subMonth()->endOfMonth()->format('d F Y').' mulai pukul 10:00',
                    'all'       => 0,
                    'antrian'   => 0,
                    'selesai'   => 0,
                    'progress'  => 0, 
                ])
            ], 200); 
        }
    }

    public function monitoring($id)
    {
        $dateNow=Carbon::now()->format('Y-m-d H:i:s');
        $jumAll=0;
        $jumCenter=0;
        $jumPakis=0;
        $jumStockAll=0;
        $jumStockCenter=0;
        $jumStockPakis=0;
        $jumAntrian=0;
        $jumsama = 0;
        $jumNsama = 0;
        $jumTemuan = 0;
        $startStock = '';
        $endStock = '';
        $interval = '';

        $data = StockOpname::findOrFail($id);
        if(isset($data)){
            $jumAll = ItemStockOpname::where([['periode',$data->periode],['year',$data->year]])->count();
            $jumCenter = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['warehouse','CENTRE']])->count();
            $jumPakis = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['warehouse','Gudang Pakis']])->count();
            $jumStockCenter = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['warehouse','CENTRE'],['stockop','!=', null],['updated_at','<=',Carbon::parse($data->created_at)->addDay()]])->count();
            $jumStockPakis = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['warehouse','Gudang Pakis'],['stockop','!=', null],['updated_at','<=',Carbon::parse($data->created_at)->addDay()]])->count();
            $jumAntrian = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['stockop', null]])->count();
            $jumStockAll = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['stockop','!=', null]])->count();
            $jumTemuan = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['id_wh', null]])->count();
            $startStock = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['stockop','!=', null]])->orderBy('updated_at','ASC')->first();
            $startStock = ($startStock) ? $startStock->updated_at : '';
            $dataTimeline = ItemStockOpname::with('users:id,name')->where([['periode',$data->periode],['year',$data->year],['stockop','!=', null]])->orderBy('updated_at','DESC')->limit(20)->get();
            $endStock = ItemStockOpname::where([['periode',$data->periode],['year',$data->year],['stockop','!=', null],['updated_at','<=',Carbon::parse($data->created_at)->addDay()]])->orderBy('updated_at','DESC')->first();
            $endStock = ($endStock) ? $endStock->updated_at : '';
            $ListParticipant = ItemStockOpname::select('post_by',DB::raw('count(*) as jumlah'))->where([['periode',$data->periode],['year',$data->year]])->groupBy('post_by')->orderBy('post_by','ASC')->get();
			$__all = ItemStockOpname::where([['periode',$data->periode],['year',$data->year]])->get();
			foreach($__all as $item)
			{
				if($item->qty == $item->stockop){
					$jumsama++;
				}elseif($item->qty != $item->stockop){
					$jumNsama++;
				}
			}

            if($data->status !='WAITING' || Carbon::now() > Carbon::parse($data->created_at)->addDay()){
                $start = Carbon::parse($startStock);
                $end = Carbon::parse($endStock);
                $tmp = $start->diff($end);
                $hours = $tmp->format("%H");
                $minutes = $tmp->format("%I");
                $seconds = $tmp->format("%S");
                $interval = array(
                    sprintf('%02d',$hours),
                    sprintf('%02d',$minutes),
                    sprintf('%02d',$seconds)
                );
                
            }
        }

        $dreturn=array(
            'data-stockop'      =>$data,
            'jumAll'            =>$jumAll,
            'jumCenter'         =>$jumCenter,
            'jumPakis'          =>$jumPakis,
            'jumStockAll'       =>$jumStockAll,
            'jumStockCenter'    =>$jumStockCenter,
            'jumStockPakis'     =>$jumStockPakis,
            'jumAntrian'        =>$jumAntrian,
            'jumsama'           =>$jumsama,
            'jumNsama'          =>$jumNsama,
            'jumTemuan'         =>$jumTemuan,
            'startStock'        =>$startStock,
            'endStock'          =>$endStock,
            'interval'          =>$interval,
            'dataTimeline'      =>$dataTimeline,
            'ListParticipant'   =>$ListParticipant
        );
        
        if($data){
            return response()->json([
                'success' => true,
                'message' =>'Data Monitoring Stock Opname',
                'data'    => $dreturn
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' =>'Tidak Ada Data Data Monitoring Stock Opname',
            ], 404);
        }
    }
    
    public function timeline()
    {
        $data = StockOpname::where([['periode',$this->periode],['year',$this->year]])->first();
        $dataTimeline = ItemStockOpname::with('users:id,name')->where([['periode',$data->periode],['year',$data->year],['stockop','!=', null]])->orderBy('updated_at','DESC')->limit(20)->get();

        $result = array();
        foreach ($dataTimeline as $item) {
            $userpostby = ($item->users) ? $item->users->name : 'System';
            $tmp=array();
            $tmp['itemno']          = $item->itemno;
            $tmp['itemdesc']        = $item->itemdesc;
            $tmp['location']        = $item->warehouse;
            $tmp['status']          = fdatastatus($data);
            $tmp['message']         = "Data Item ".$item->itemno." - ".$item->itemdesc." telah berhasil diperbarui oleh ".$userpostby;
            $tmp['updated_at']      = Carbon::parse($item->updated_at)->diffForHumans(); 

            array_push($result, $tmp);
        }

        return response()->json([
            'success' => true,
            'message' =>'Data Timeline Stock Opname',
            'data'    => $result
        ],200);
    }
}
