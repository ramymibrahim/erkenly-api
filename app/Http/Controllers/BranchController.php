<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use DB;
class BranchController extends Controller
{
    //

    public function index(Request $request)
    {
        $data = $request->all();
        $branches = Branch::where('capacity', '>', 0)
        ->where('hours_from','<=',$data['currentTime'])
        ->where('hours_to','>=',$data['currentTime'])
        ->orderBy(DB::raw('ABS(lat - '.(double)$data['lat'].') + ABS(lng - '.(double)$data['lng'].')'),'ASC' )
        ->get();
        return response()->json($branches,200);        
    }
}
