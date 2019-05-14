<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RegController extends Controller
{
    public function register(Request $request){
        $user_name=$request->input('user_name');
        $user_email=$request->input('user_email');
        $user_pwd=$request->input('user_pwd');
        $where=[
            'user_email'=>$user_email
        ];
        $dataInfo=DB::table('register')->where($where)->first();
        if($dataInfo){
            $res=[
                'code'=>40002,
                'msg'=>'此邮箱已存在'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
        $arrInfo=[
            'user_name'=>$user_name,
            'user_email'=>$user_email,
            'user_pwd'=>password_hash($user_pwd,PASSWORD_BCRYPT)
        ];
        $arr=DB::table('register')->insert($arrInfo);
        if($arr){
            $res=[
                'code'=>200,
                'msg'=>'注册成功'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }else{
            $res=[
                'code'=>40002,
                'msg'=>'注册失败'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }
    public function login(Request $request){
        $user_name=$request->input('user_name');
        $user_pwd=$request->input('user_pwd');
        $where=[
            'user_name'=>$user_name
        ];
        $dataInfo=DB::table('register')->where($where)->first();
        if($dataInfo){
            if(password_verify($user_pwd,$dataInfo->user_pwd)){
                $res=[
                    'code'=>200,
                    'msg'=>'登录成功'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }else{
                $res=[
                    'code'=>40020,
                    'msg'=>'登录失败'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $res=[
                'code'=>40010,
                'msg'=>'没有此账号'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }
}