<?php

namespace App\Http\Controllers\decrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DecryptController extends Controller
{
    /**
     * 对称解密
     */
    public function decrypt(){
        $res=file_get_contents("php://input");
        $d64=base64_decode($res);
        $method="AES-256-CBC";
        $key="124abc";
        $option=OPENSSL_RAW_DATA;
        $iv="12345tgvfred2346";

        $pass=openssl_decrypt($d64,$method,$key,$option,$iv);
        var_dump($pass);
    }

    /**
     * 非对称解密
     */
    public function rsa(){
        //解密
        $enc_data=file_get_contents("php://input");
        $pk=openssl_get_publickey('file://'.storage_path('app/key/public.pem'));
        openssl_public_decrypt($enc_data,$dec_data,$pk);
        echo "<hr/>";
        echo $dec_data;
    }

    /**
     * 验签
     */
    public function verify(){
        $str=file_get_contents("php://input");
        $rec_sign=$_GET['sign'];
        $pk=openssl_get_publickey('file://'.storage_path('app/key/public.pem'));
        $rs=openssl_verify($str,base64_decode($rec_sign),$pk);
        var_dump($rs);
    }
}
