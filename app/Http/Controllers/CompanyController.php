<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Company;
class CompanyController extends Controller
{
    //

    public function index(){        
        return response()->json(Company::all(), 400);
    }
}
