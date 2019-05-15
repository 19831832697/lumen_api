<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $u_token=$_GET['token'];
        $user_id=$_GET['user_id'];
        $key="token$user_id";
        $token=Redis::get($key);
        if($token){
            if($token==$u_token){

            }else {
                $response = [
                    'code' => 50002,
                    'msg' => '不合法的token值'
                ];
                return json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }else{
            $response = [
                'code' => 50002,
                'msg' => '请先登录'
            ];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        return $next($request);
    }
}