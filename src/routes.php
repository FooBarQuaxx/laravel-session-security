<?php

use Config;

Route::get(Config::get('laravel-session-security::config.ping_url'), [
    'as' => Config::get('laravel-session-security::config.ping_route_name'), 
    'uses' => 'MedAbidi\LaravelSessionSecurity\Controllers\PingController@ping'
]);
