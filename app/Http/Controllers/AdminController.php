<?php

namespace App\Http\Controllers;

use App\User;
use \Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class AdminController extends Controller
{
    public function tambahAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required | unique:users',
            'password' => 'required',
            'rememberToken' => 'required',
        ]);

        if ($validator->fails()) {
          return response()->json([
            'status' => 'gagal',
            'message' => $validator->messages()
          ]);
        }

        $token = $request->token;
        $tokenDb = User::where('rememberToken', $token)->count();

        if ($tokenDb > 0) {
          $key = env('APP_KEY');
          $decode = JWT::decode($token, $key, array('HS256'));
          $decode_array = (array) $decoded;
          if ($decode_array['extime'] > time()) {
            if (User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => encrypt($request->password),
              ])){
            }else{
                return response()->json([
                  'status' => 'berhasil',
                  'message' => 'Data gagal di simpan',
                ]);
            }
          }
        }else{
          return response()->json([
            'status' => 'berhasil',
            'message' => 'Maaf your token kaduwarlsa.!',
          ]);
        }
    }
}
