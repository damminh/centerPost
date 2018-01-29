<?php

namespace App\Http\Controllers\users;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $params = $request->all();
        // $validate = Validator::make($params, [
        //     'username' => 'required|unique:users|max:255',
        //     'password' => 'required|max:255',
        // ]);
        // if ($validate->fails()) {
        //     response()->json(['message' => 'error'], 500);
        // }

        $user = User::where('username', $params['username'])->first();
        if (!$user) {
            return response()->json(['message' => 'error'], 500);
        }
        $check = Hash::check($params['password'], $user->password);
        if (!$check) {
            return response()->json(['message' => 'error'], 500);
        }

        return response()->json([
            'token' => $user->token,
            'name' => $user->name,
        ], 200);
    }

    public function logout()
    {
        return response(['message' => 'success'], 200);
    }
}
