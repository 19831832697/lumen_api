<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class RegController extends Controller
{
    /**
     * 注册接口
     * @param Request $request
     * @return false|string
     */
    public function register( Request $request){
       $arrInfo=$request->input();
       $arr=json_encode($arrInfo);

        $url="http://pass.1809a.com/reg";
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type:text/plain']);
        $res=curl_exec($ch);
        echo $res;
    }

    /**
     * 登录接口
     * @param Request $request
     * @return false|string
     */
    public function login(Request $request){
       $dataInfo=$request->input();
       $data=json_encode($dataInfo);

        $url="http://pass.1809a.com/login";
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type:text/plain']);
        $res=curl_exec($ch);
        echo $res;
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

    /**
     * 个人中心
     * @param Request $request
     */
    public function userInfo(Request $request){
        $user_id=$_GET['user_id'];
        $where=[
            'user_id'=>$user_id
        ];
        $dataInfo=DB::table('register')->where($where)->first();
        $data=json_encode($dataInfo);
        return $data;
    }
}