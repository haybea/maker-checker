<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        //Validate data
        $data = $request->only( 'email', 'password');
        $validator = Validator::make($data, [
            'email' => 'required|email|unique:admins',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        //Request is valid, create new user
        $admin = Admin::create([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //Admin created, return success response
        return $this->successWithData($admin,'Admin created successfully');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
//            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Create token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->error('Login credentials are invalid.');
            }
        } catch (JWTException $e) {
            return $this->error('Could not create token.', 500);
        }

        //Token created, return with success response and jwt token
        return $this->successWithData(['token' => $token,],'Login Successful');
    }

    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        //Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);
            return $this->success('Admin has been logged out');
        } catch (JWTException $exception) {
            return $this->error('Sorry, user cannot be logged out',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

//    public function get_user(Request $request)
//    {
//        $this->validate($request, [
//            'token' => 'required'
//        ]);
//
//        $user = JWTAuth::authenticate($request->token);
//
//        return response()->json(['user' => $user]);
//    }
}