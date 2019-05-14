<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class LoginTokenMiddleware
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
        $user_id=$_GET['user_id'];
        $u_token=$_GET['token'];
        if(empty($token) || empty($user_id)){
            $res=[
                'errno'=>40003,
                'msg'=>'参数不全'
            ];
            die(json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        $key="token$user_id";
        $token=Redis::get($key);
        if($token){
            if($token==$u_token){

            }else {
                $response = [
                    'code' => 50002,
                    'msg' => '不合法的token值'
                ];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }else{
            $response = [
                'code' => 50002,
                'msg' => '请先登录'
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        return $response;
    }
}