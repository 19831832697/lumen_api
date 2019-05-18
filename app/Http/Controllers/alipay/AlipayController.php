<?php

namespace App\Http\Controllers\alipay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AlipayController extends Controller
{
    public function zPay(Request $request){
        $order_no=$request->input('orderno');
//        echo $order_no;die;
        $dataInfo=DB::table('shop_order')->where('order_no',$order_no)->first();
        $order_id=$dataInfo->order_id;


        $url="http://passport.ffddd.top/zPay?order_id=$order_id";
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $res=curl_exec($ch);
//        $errno=curl_errno($ch);
//        echo $errno;
        var_dump($res);
    }
}
