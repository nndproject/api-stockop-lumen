<?php

namespace App\Http\Controllers\StockOpname;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            ], 204);
        }
    }
}
