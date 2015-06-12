<?php namespace MedAbidi\LaravelSessionSecurity\Controllers;

use Session,
    Response,
    Controller,
    Log;

use Carbon\Carbon;

use MedAbidi\LaravelSessionSecurity\Utils;

class PingController extends Controller {


    public function ping()
    {
        if(!Session::has('_session_security')) {
            return Response::make('logout');
        }

        $lastActivity = Utils::getLastActivity(Session::all());
        $inactiveFor = Carbon::now()->diffInSeconds($lastActivity);
        return Response::make($inactiveFor);
    }

}

