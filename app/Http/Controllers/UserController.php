<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $checkEmail = User::where('email', '=', $request->get('email'))->get();
        if (count($checkEmail) >= 1) {
            if (Hash::check($request->get('password'), $checkEmail[0]->password)) {
                $getUser = [
                    'id' => $checkEmail[0]->id,
                    'fullname' => $checkEmail[0]->fullname,
                    'email' => $checkEmail[0]->email,
                ];
                $return = [
                    'response' => 200,
                    'message' => 'Success Login',
                    'data' => $getUser,
                ];
                return response()->json($return, 200);
            } else {
                $return = [
                    'response' => 404,
                    'message' => 'Wrong Password',
                    'data' => '',
                ];
                return response()->json($return, 404);
            }
        } else {
            $return = [
                'response' => 404,
                'message' => 'Email Not Registred',
                'data' => '',
            ];
            return response()->json($return, 404);
        }
    }
    public function register(Request $request)
    {
        $userValidate = User::where('email', '=', $request->get('email'))->get();
        // return response()->json(count($userValidate), 200);
        if (count($userValidate) >= 1) {
            $return = [
                'response' => 400,
                'message' => 'Email Already Registred',
                'data' => '',
            ];
            return response()->json($return, 400);
        } else {
            $user = [
                'fullname' => $request->get('fullname'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ];
            $userCreate = User::create($user);
            $token = JWTAuth::fromUser($userCreate);
            $return = [
                'response' => 200,
                'message' => 'Success Get Data User',
                'data' => $user,
                'Token' => $token
            ];
            return response()->json($return, 200);
        }
    }

    public function view(Request $request, string $user)
    {
        $users = User::where('fullname', 'like', '%' . $user . '%')->get();
        $getUsers = [];
        for ($i = 0; $i < count($users); $i++) {
            $getUsers[$i] = [
                'fullname' => $users[$i]->fullname,
                'email' => $users[$i]->email,
                'device' => $users[$i]->device,
                'phone' => $users[$i]->phone->number,

            ];
        };
        $return = [
            'response' => 200,
            'message' => 'Success Get Data User',
            'data' => $getUsers,
        ];

        return response()->json($return, 200);
    }
}
