<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * 注册执行接口
     * @param Request $request
     * @return false|string
     */
    public function regDo(Request $request){
        $enc_data=file_get_contents("php://input");
        $pk=openssl_get_publickey('file://'.storage_path('app/key/public.pem'));
        openssl_public_decrypt($enc_data,$dec_data,$pk);
        $data=json_decode($dec_data,true);

        $where=[
            'user_email'=>$data['user_email']
        ];
        $dataInfo=DB::table('reg')->where($where)->first();
        if($dataInfo){
            $res=[
                'code'=>40001,
                'msg'=>'此邮箱已存在',
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
        $arrInfo=[
            'user_name'=>$data['user_name'],
            'user_email'=>$data['user_email'],
            'user_pwd'=>password_hash($data['user_pwd'],PASSWORD_BCRYPT),
        ];
        $arr=DB::table('reg')->insert($arrInfo);
        if($arr){
            $res=[
                'code'=>200,
                'msg'=>'注册成功',
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }else{
            $res=[
                'code'=>40002,
                'msg'=>'注册失败',
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 登录执行接口
     * @return false|string
     */
    public function loginDo(){
//        header('Access-Control-Allow-Origin:http://client.1809a.com');
        echo 5555;die;
        $data=file_get_contents("php://input");
        $dataInfo=openssl_get_publickey('file://'.storage_path('app/key/public.pem'));
        openssl_public_decrypt($data,$dec_data,$dataInfo);
        $arrInfo=json_decode($dec_data,true);
        $pwd=$arrInfo['user_pwd'];
        $where=[
            'user_name'=>$arrInfo['user_name'],
        ];
        $arr=DB::table('reg')->where($where)->first();
        if($arr){
            $user_name=$arr->user_name;
            $user_pwd=$arr->user_pwd;
            $user_id=$arr->user_id;
        }

        if(empty($user_name)){
            $res=[
                'code'=>40009,
                'msg'=>'此用户不存在'
            ];
        }else if(password_verify($pwd,$user_pwd)){
            $token=$this->token($user_id,$user_name);
            $key="token".$user_id;
            Redis::set($key,$token);
            Redis::expire($key,60*60*27*7);
            $res=[
                'code'=>200,
                'msg'=>'登录成功',
                'data'=>[
                    'token'=>$token
                ],
            ];
        }else{
            $res=[
                'code'=>40005,
                'msg'=>'账号或密码错误'
            ];
        }
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 生成token
     * @param $user_id
     * @param $user_name
     * @return string
     */
    public function token($user_id,$user_name){
        return substr(sha1(Str::random(11).md5($user_name)),5,15)."user_id".$user_id;
    }
}