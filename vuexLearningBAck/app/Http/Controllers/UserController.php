<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function self()
    {
        $user = User::find(auth()->user()->id);
        $token = $user->createToken('authToken')->accessToken;
        return response(['user' => $user, 'access_token' => $token]);
    }
    
    public function login(Request $request)
    {
        $login = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email',$request->email)->first();
        if (!Auth::attempt( $login ))
        {
            return response(['message' => 'login Credentials are incorrect'], 500);
        }
        $token = $user->createToken('authToken')->accessToken;
        return response(['user' => Auth::user(), 'access_token' => $token]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->tokem()->delete();
    }
    public function index(){
        return User::get();
    }

    public function saveNew(Request $request) 
    {
        $user = New User;
        $user->lastname = $request->lastname;
        $user->firstname = $request->firstname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        if($request->user_type){
            $user->user_type = $request->user_type;
        }
        $user->save();
        return $user;

    }

    public function editUser(Request $request, $id){
        $user = User::findorfail($id);
        $user->lastname = $request->lastname;
        $user->firstname = $request->firstname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if($request->user_type){
            $user->user_type = $request->user_type;
        }
        $user->save();
        return $user;

    }
    public function pagination(Request $request){
        $users = User::query();
        $users->where('user_type', 0);
        if($request->input('keyword') != ""){
            $keyword = $request->input('keyword');
            $users->where(function($query) use($keyword){
                $query   ->where('firstname', 'LIKE', "%$keyword%");
                      
            });
        }
        return $users->orderBy('firstname', 'asc')->paginate(10);
    }
    public function search(Request $request) 
    {
        $user = User::query();
        $user->where('user_type', 0);
        if ($request->input('searchkey') != "") {
            $keyword = $request->input('searchkey');
            $user->where(function($query) use($keyword) {
                $query  ->where('firstname', 'LIKE', "%$keyword%")
                        ->orWhere('lastname', 'LIKE', "%$keyword%")
                        ->orWhere('email', 'LIKE', "%$keyword%");
                         
            });
        }
        return $user->orderBy('first_name', 'asc')->get();
    }

    public function deleteUser($id){
        $user = User::find($id);
        $user->delete();
        return 'Deleted';
    }

}
