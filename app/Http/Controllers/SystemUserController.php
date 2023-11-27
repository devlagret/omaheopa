<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\CoreDivision;
use Illuminate\Http\Request;
use App\Models\SalesMerchant;
use App\Models\SystemUserGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use JeroenNoten\LaravelAdminLte\Components\Widget\Alert;

class SystemUserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $systemuser = User::with('merchant')->where('data_state','=','0')
        ->where('company_id', Auth::user()->company_id)
        ->where('user_level', 0)
        ->get();
        return view('content/SystemUser/ListSystemUser',compact('systemuser'));
    }

    public function addSystemUser(Request $request)
    {
        $systemusergroup    = SystemUserGroup::where('data_state','=','0')
        ->where('company_id', Auth::user()->company_id)
        ->where('user_group_status', '0')
        ->get();
        $merchant   = SalesMerchant::where('data_state','=','0')
        ->get();
        return view('content/SystemUser/FormAddSystemUser',compact('systemusergroup', 'merchant'));
    }

    public function processAddSystemUser(Request $request)
    {
        $fields = $request->validate([
            'name'                  => 'required',
            'full_name'             => 'required',
            'password'              => 'required',
            'user_group_id'         => 'required',
            'merchant_id'            => 'required'
        ]);
        try {
        DB::beginTransaction();
        User::create([
            'name'                  => $fields['name'],
            'full_name'             => $request->full_name,
            'password'              => Hash::make($fields['password']),
            'user_group_id'         => $fields['user_group_id'],
            'merchant_id'            => $fields['merchant_id'],
            'user_token'            => Str::uuid(),
            'company_id'            => Auth::user()->company_id,
            'created_id'            => Auth::id(),
        ]);
        // 'phone_number'          => $request->phone_number,

        DB::commit();
        return redirect()->route('system-user')->with('msg',"Tambah System User Berhasil");
        } catch (\Exception $e) {
        DB::rollBack();
        dd($e);
        report($e);
        return redirect()->route('system-user')->with('msg',"Tambah System User Gagal");
        }
    }

    public function editSystemUser($user_id)
    {
        $systemusergroup    = SystemUserGroup::where('data_state','=','0')
        ->where('company_id', Auth::user()->company_id)
        ->where('user_group_status', '0')
        ->get()
        ->pluck('user_group_name','user_group_id');
        $systemuser         = User::where('user_id',$user_id)->first();
        $merchant        = SalesMerchant::where('data_state','=',0)
        ->get()
        ->pluck('merchant_name','merchant_id');
        // $merchant[0]     = "Multi Section";
        return view('content/SystemUser/FormEditSystemUser',compact('systemusergroup', 'systemuser', 'user_id', 'merchant'));
    }

    public function processEditSystemUser(Request $request)
    {
        $fields = $request->validate([
            'user_id'                   => 'required',
            'name'                      => 'required',
            'full_name'                 => 'required',
            'user_group_id'             => 'required',
            'merchant_id'                => 'required'
        ]);

        $user                   = User::findOrFail($fields['user_id']);
        $user->name             = $fields['name'];
        $user->full_name        = $fields['full_name'];
        if($request->password){
            $user->password         = Hash::make($request->password);
        }
        $user->user_group_id    = $fields['user_group_id'];
        $user->merchant_id       = $fields['merchant_id'];
        $user->updated_id       = Auth::id();
        // $user->phone_number     = $request->phone_number;

        if($user->save()){
            $msg = 'Edit System User Berhasil';
            return redirect()->route('system-user')->with('msg',$msg);
        }else{
            $msg = 'Edit System User Gagal';
            return redirect()->route('system-user')->with('msg',$msg);
        }
    }

    public function deleteSystemUser($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->data_state = 1;
        if($user->save())
        {
            $msg = 'Hapus System User Berhasil';
        }else{
            $msg = 'Hapus System User Gagal';
        }

        return redirect()->route('system-user')->with('msg',$msg);
    }

    public function getUserGroupName($user_group_id)
    {
        $usergroupname =  User::select('system_user_group.user_group_name')->join('system_user_group','system_user_group.user_group_id','=','system_user.user_group_id')->where('system_user.user_group_id','=',$user_group_id)->first();

        return $usergroupname['user_group_name'];
    }

    public function changePassword($user_id)
    {

        return view('content.SystemUser.FormChangePassword', compact('user_id'));

    }

    public function processChangePassword(Request $request)
    {

        // User::find(auth()->user()->user_id)->update([
        //     'password'=> Hash::make($request->new_password)
        //     ]);

        $request->validate([
            'password' => 'required',
            'new_password' => 'required',

        ]);

        if(Hash::check($request->password, Auth::user()->password))
        {
            User::find(auth()->user()->user_id)->update([
            'password'=> Hash::make($request->new_password)
            ]);
            $msg = "Password Berhasil Diubah";
            return redirect()->back()->with('msg',$msg);
        }else{
            $msg = "Password Lama Tidak Cocok";
            return redirect()->back()->with('msg',$msg);
        }





    }
}
