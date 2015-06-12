<?php namespace MedAbidi\LaravelSessionSecurity\Middleware;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use Carbon\Carbon;

use MedAbidi\LaravelSessionSecurity\Utils as SessionSecurityUtils;

use Config,
    Route,
    Auth,
    Session,
    Log;

class SessionSecurity implements HttpKernelInterface {

    /**
     * The wrapped kernel implementation.
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected $app;

    /**
     * Create a new RateLimiter instance.
     *
     * @param  \Symfony\Component\HttpKernel\HttpKernelInterface  $app
     * @return void
     */
    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Handle the given request and get the response.
     *
     * @implements HttpKernelInterface::handle
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  int   $type
     * @param  bool  $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = $this->app->handle($request, $type, $catch);

        if(Auth::guest()) {
            return $response;
        }
        
        $now = Carbon::now();
        $this->updateLastActivity($request, $now);

        $delta = $now->diffInSeconds(SessionSecurityUtils::getLastActivity(Session::all()));
        $expire_after = Config::get('laravel-session-security::config.expire_after');

        Log::debug('SessionSecurityMiddleware#handle' . ':' . __LINE__ . ' ' .
            json_encode(compact('delta', 'expire_after'))
        );

        if($delta >= $expire_after) {
            Session::forget('_session_security');
            Auth::logout();
        } else if(!$this->isPassiveRequest()) {
            SessionSecurityUtils::setLastActivity($now);
        }

        return $response;
    }

    protected function updateLastActivity($request, $time)
    {
        if(!Session::has('_session_security')) {
            SessionSecurityUtils::setLastActivity($time);
        }

        $lastActivity = SessionSecurityUtils::getLastActivity(Session::all());
        $serverIdleFor = $time->diffInSeconds($lastActivity);
        if( Route::currentRouteName() == 'session_security_ping' && 
            $request->get('idleFor', FALSE)) {
                $clientIdleFor = max(0, intval($request->get('idleFor')));
                if($clientIdleFor < $serverIdleFor) {
                    $lastActivity = $time->subSeconds($clientIdleFor);
                    SessionSecurityUtils::setLastActivity($lastActivity);
                }
        }

    }

    protected function isPassiveRequest()
    {
        $passiveRoutes = Config::get('laravel-session-security::config.passive_routes');
        $pingRouteName = Config::get('laravel-session-security::config.ping_route_name');
        array_push($passiveRoutes, $pingRouteName);
        return in_array(Route::currentRouteName(), $passiveRoutes);
    }

}
