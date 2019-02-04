<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use \App\Company;
use \App\Branch;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function registerClient(Request $request){
        $data=$request->json()->all();        
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|regex:/(01)[0-9]{9}/|unique:users|max:11',            
        ]);
        if(!$validator->fails()){
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'mobile'=>$data['mobile'],
                'type'=>'client'
            ]);           
            return response()->json($user, 201);
        }
        else
        {
            return response()->json($validator->errors(), 400);            
        }        
    }


    public function registerCompany(Request $request){
        $data=$request->json()->all();        
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|regex:/(01)[0-9]{9}/|unique:users|max:11',
            'image'=>'required',
            'capacity'=>'required',
            'hour_price'=>'required',
            'lng'=>'required',
            'lat'=>'required',
            'hours_from'=>'required',
            'hours_to'=>'required'
        ]);
        if(!$validator->fails()){
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'mobile'=>$data['mobile'],
                'type'=>'company'
            ]);
            if($user){
                $image = $data['image'];  // your base64 encoded
                $ext='';
                if(strpos($image,'image/png')){
                    $ext='.png';
                }
                else if(strpos($image,'image/jpg')){
                    $ext='.jpg';
                }
                else if(strpos($image,'image/jpeg')){
                    $ext='.jpeg';
                }
                else{
                    $user->destroy();
                    return response()->json('Please select a valid image (png,jpg or jpeg)', 400);                    
                }
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = str_random(10).'_'.$user['id'].$ext;
                \File::put(storage_path(). '/app/public/' . $imageName, base64_decode($image));
                $company = Company::create([
                    'user_id'=>$user['id'],
                    'image'=>$imageName
                ]);
                if($company){
                    $branch=Branch::create([
                        'company_id'=>$company['id'],
                        'capacity'=>$data['capacity'],
                        'hour_price'=>$data['hour_price'],
                        'lng'=>$data['lng'],
                        'lat'=>$data['lat'],
                        'hours_from'=>$data['hours_from'],
                        'hours_to'=>$data['hours_to'],
                    ]);
                    if(!$branch){
                        $company->destroy();
                        $user->destroy();
                        \File::delete(storage_path(). '/' . $imageName);
                        return response()->json('Error while creating Branch', 400);                        
                    }
                    else{
                        return response()->json($user, 201);
                    }
                }
                else{
                    $user->destroy();
                    return response()->json('Error while creating the company', 400);
                }
            }
        }
        else
        {
            return $validator->errors();
        }        
    }
}