<?php namespace MedAbidi\LaravelSessionSecurity;

use Carbon\Carbon;

use Session;

class Utils {

    public static function getLastActivity($session)
    {
        try {
            return Carbon::createFromFormat('Y-m-d H:i:s.u T', $session['_session_security']);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    public static function setLastActivity($dt)
    {
        Session::put('_session_security', $dt->format('Y-m-d H:i:s.u T'));
    }
}

