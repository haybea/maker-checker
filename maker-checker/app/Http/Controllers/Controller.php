<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\Admin;
use App\Utils\JSONResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,JSONResponse;

    public function sendMail($admin_id){
        $admins = Admin::where('id','!=',$admin_id)->get();
        foreach ($admins as $admin){
            SendEmailJob::dispatch($admin->email);
        }
    }
}
