<?php
namespace App\Http\Controllers\login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * 登录接口
     * @param Request $request
     * @return false|string
     */
    public function loginDo(Request $request)
    {
        $arrInfo = file_get_contents("php://input");
        //非对称解密
        $k = openssl_get_publickey("file://".storage_path('app/key/public.pem'));
        openssl_public_decrypt($arrInfo,$des_data,$k);
//        var_dump($des_data);die;
        //对称解密
//        $method = "AES-256-CBC";
//        $key = "Admin123";
//        $options = OPENSSL_RAW_DATA;
//        $iv="12345tgvfred2346";
//        $data = openssl_decrypt($arrInfo,$method,$key,$options,$iv);
        $dataInfo = json_decode($des_data,true);
//        var_dump($dataInfo);die;
        $user_name = $dataInfo['user_name'];
        $user_pwd = $dataInfo['user_pwd'];
        $where = [
            'user_name'=>$user_name
        ];
        $userInfo = DB::table('user')->where($where)->first();
//        var_dump($userInfo);die;
        if($userInfo){
            $user_id = $userInfo->user_id;
            if($user_pwd == $userInfo->user_pwd){
                $key = "user_token".$user_id;
                $token = substr(sha1(Str::random(11).md5($user_name)),5,15)."user_id".$user_id;
                Redis::set($key,$token);
                Redis::expire($key,60*60*24*7);
                $arr = [
                    'code'=>0,
                    'msg'=>'登录成功'
                ];
                return json_encode($arr,JSON_UNESCAPED_UNICODE);
            }else{
                $arr = [
                    'code'=>1,
                    'msg'=>'密码错误'
                ];
                return json_encode($arr,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $arr = [
                'code'=>1,
                'msg'=>'账号不存在'
            ];
            return json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
    }
}