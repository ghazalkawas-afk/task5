<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //cors
    private function addCorsHeaders($response)
    {
        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
    public function register(Request $request):JsonResponse {

        $request->validate(['first_name'=>['required'],
            'last_name'=>['required'],
            'mobile'=>['required','unique:users','digits:10'],
            'password'=>['required']]);
       $user= User::query()->create(['first_name'=>$request['first_name'],
            'last_name'=>$request['last_name'],
            'mobile'=>$request['mobile'],
            'mobile.unique'=>['mobile does not unique'],
            'password'=>$request['password']]);
        $token=$user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['token'] = $token;
        $data['user'] = $user;
       return response()->json(['status'=>1,
           'data'=>$data,
           'message'=>'user created Successfully']);

    }
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'mobile'=>['required','exists:users','digits:10'],
            'password'=>['required']]);
        if(!auth()->attempt(['mobile'=>$request['mobile'],'password'=>$request['password']])){
            $masseg='Mobile and password do not match';
            return response()->json(['status'=>0,
                'message'=>$masseg,
                'data' =>[]],500);
        }
       $user = User::query()->where('mobile','=',$request['mobile'])->first();
        $token=$user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['token'] = $token;
        $data['user'] = $user;
        return response()->json(['status'=>1,
            'data'=>$data,
            'message'=>'user created Successfully']);
    }
    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return response()->json(['status'=>1,
            'data'=>[],
            'message'=>'user created Successfully']);
    }
}
