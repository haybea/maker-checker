<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminRequest;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    protected $admin;
    public function __construct() {
//        $this->user = JWTAuth::parseToken()->authenticate();
        $this->admin = Auth::user();
    }

    public function pendingRequests(){
        $pending_requests = AdminRequest::where('status','pending');
        if ($pending_requests->exists()){
            return $this->successWithData($pending_requests->get());
        } else{
            return $this->success('No Pending requests');
        }
    }

    public function allUsers(){
        $users = User::all();
        if($users->count()>0){
            return $this->successWithData($users);
        } else{
            return $this->success('No users available');
        }
    }

    public function addUserRequest(Request $request){
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'firstname' => 'required|string|min:2|max:50',
            'lastname' => 'required|string|min:2|max:50',
        ]);
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        $new_request = new AdminRequest();
        $new_request->maker_id = $this->admin->id;
        $new_request->request_type = 'create';
        $new_request->payload = $credentials;
        $new_request->status = 'pending';

        if($new_request->save()){
            return $this->success('Request submitted successfully. Please wait for approval');
        } else{
            return $this->error();
        }
    }

    public function updateUserRequest(Request $request){
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'user_id' => 'required|',
            'email' => 'email',
            'firstname' => 'string|min:2|max:50',
            'lastname' => 'string|min:2|max:50',
        ]);
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        $user = User::where('id', $credentials['user_id']);

        if ($user->exists()) {

            $new_request = new AdminRequest();
            $new_request->maker_id = $this->admin->id;
            $new_request->user_id = $credentials['user_id'];
            $new_request->request_type = 'update';
            $new_request->payload = $credentials;
            $new_request->status = 'pending';

            if ($new_request->save()) {
                return $this->success('Request submitted successfully. Please wait for approval');
            } else {
                return $this->error();
            }
        }else{
            return $this->error('User not found');
        }
    }

    public function deleteUserRequest($user_id){
        $user = User::where('id', $user_id);

        if ($user->exists()) {
            $new_request = new AdminRequest();
            $new_request->maker_id = $this->admin->id;
            $new_request->user_id = $user_id;
            $new_request->request_type = 'delete';
            $new_request->status = 'pending';

            if($new_request->save()){
                return $this->success('Request submitted successfully. Please wait for approval');
            } else{
                return $this->error();
            }
        }else{
            return $this->error('User not found');
        }
    }
    public function approveRequest($request_id){
        $admin_request = AdminRequest::where('id',$request_id);
        if ($admin_request->exists()) {
            $admin_request = $admin_request->first();
            if($admin_request->maker_id!=$this->admin->id){
                if ($admin_request->status=='pending'){
                    //TODO use queue
                    $fulfill = $this->runQuery($admin_request->payload, $admin_request->request_type,$admin_request->user_id);
                    if ($fulfill===true){
                        $admin_request->status = 'approved';
                        $admin_request->checker_id = $this->admin->id;
                        if ($admin_request->update()){
                            return $this->success();
                        }
                    }else{
                        return $this->error('Something went wrong. Please try again');
                    }
                }else{
                    return $this->error('Request already '.$admin_request->status);
                }
            } else{
                return $this->error('You are not authorised to approve this request');
            }
        }else{
            return $this->error('Request not found');
        }
    }
    public function declineRequest($request_id){
        $admin_request = AdminRequest::where('id',$request_id);
        if ($admin_request->exists()) {
            $admin_request = $admin_request->first();
            if($admin_request->maker_id!=$this->admin->id){
                if ($admin_request->status=='pending'){
                    $admin_request->status = 'declined';
                    $admin_request->checker_id = $this->admin->id;
                    if ($admin_request->update()){
                        return $this->success('Decline successful');
                    } else{
                        return $this->error('Something went wrong. Please try again');
                    }
                }else{
                    return $this->error('Request already '.$admin_request->status);
                }
            } else{
                return $this->error('You are not authorised to approve this request');
            }
        }else{
            return $this->error('Request not found');
        }
    }

    public function runQuery($data, $request_type,$user_id){
        if ($request_type=='create'){
            $user = new User();
            $user->email = $data['email'];
            $user->firstname = $data['firstname'];
            $user->lastname = $data['lastname'];

            if($user->save()){
                return true;
            } else{
                return false;
            }
        } elseif($request_type=='update'){
            unset($data['user_id']);
            $user = User::where('id',$user_id)->update($data);
            if($user){
                return true;
            } else{
                return false;
            }
        }elseif($request_type=='delete'){
            if(User::where('id',$user_id)->delete()){
                return true;
            } else{
                return false;
            }
        }
        return false;
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