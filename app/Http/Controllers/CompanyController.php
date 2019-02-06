<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CompanyController extends Controller
{
    public function index()
    {
        $user = \Request::get('user');        
        $company = Company::where('user_id', $user['id'])->first();
        $company['branches'] = $company->branches()->get();
        return response()->json($company, 200);
    }

    public function changeCapacity(Request $request)
    {
        return $this->change($request, 'capacity');
    }

    public function changePrice(Request $request)
    {
        return $this->change($request, 'price');
    }

    public function changeHours(Request $request)
    {
        return $this->change($request, 'hours');
    }

    public function addBranch(Request $request)
    {
        $data=$request->all();        
        $validator = Validator::make($data, [
            'capacity' => 'required',
            'hour_price' => 'required',
            'lng' => 'required',
            'lat' => 'required',
            'hours_from' => 'required',
            'hours_to' => 'required',
        ]);
        if (!$validator->fails()) {
            $user = \Request::get('user');
            $user_id = $user['id'];
            $company = Company::where('user_id', $user_id)->first();
            $branch=Branch::create([
                'company_id'=>$company['id'],
                'capacity'=>$data['capacity'],
                'hour_price'=>$data['hour_price'],
                'lng'=>$data['lng'],
                'lat'=>$data['lat'],
                'hours_from'=>$data['hours_from'],
                'hours_to'=>$data['hours_to'],
            ]);
            return response()->json($branch, 201);
        }   
        else{
            return response()->json('Error while creating Branch', 400);
        }    
    }

    private function change($request, $type)
    {
        $user = \Request::get('user');
        $branchId = $request->input('branch_id');
        if (!$this->checkBranch($branchId, $user)) {
            return response()->json(['invalid_branch'], 422);
        }
        $branch = Branch::findOrFail($branchId);
        if ($type == 'price') {
            $branch['hour_price'] = $request->input('hour_price');
        } else if ($type == 'capacity') {
            $branch['capacity'] = $request->input('capacity');
        } else if ($type == 'hours') {
            $branch['hours_from'] = $request->input('hours_from');
            $branch['hours_to'] = $request->input('hours_to');
        }
        $branch->save();
        return $branch;
    }

    private function checkBranch($branchId, $user)
    {
        $user_id = $user['id'];
        $company = Company::where('user_id', $user_id)->first();
        $branch = Branch::find($branchId);
        if (!$branch) {
            return false;
        }

        return $branch['company_id'] == $company['id'];
        /*
    if($branch['company_id']==$company['id'])
    return true;
    else
    return false;
     */
    }    

}
