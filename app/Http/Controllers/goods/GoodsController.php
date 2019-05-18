<?php

namespace App\Http\Controllers\goods;

use App\model\CartModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class GoodsController extends Controller
{
    /**
     * 商品展示
     * @return false|string
     */
   public function goods(){
        $dataInfo=DB::table('shop_goods')->get();
        $data=json_encode($dataInfo,JSON_UNESCAPED_UNICODE);
        return $data;
   }

    /**
     * 商品详情
     * @param Request $request
     * @return false|string
     */
   public function goodsInfo(Request $request){
        $goods_id=$request->input('goods_id');
        $where=[
            'goods_id'=>$goods_id
        ];
        $dataInfo=DB::table('shop_goods')->where($where)->first();
        $data=json_encode($dataInfo,JSON_UNESCAPED_UNICODE);
        return $data;
   }

    /**
     * 加入购物车
     * @param Request $request
     * @return mixed
     */
   public function cart(Request $request){
       $goods_id=$request->input('goods_id');
       $user_id=$request->input('user_id');
       $arrInfo=[
           'goods_id'=>$goods_id,
           'user_id'=>$user_id
       ];
       $arr=json_encode($arrInfo);

       $url="http://pass.1809a.com/cart";
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
     * 购物车列表
     * @param Request $request
     */
   public function cartInfo(Request $request){
       $user_id=$request->input('user_id');
       if(empty($user_id)){
           $res=[
               'code'=>40005,
               'msg'=>'请先登录'
           ];
           return json_encode($res,JSON_UNESCAPED_UNICODE);
       }
       $where=[
           'user_id'=>$user_id,
           'status'=>1
       ];
       $dataInfo=DB::table('shop_cart')
               ->join('shop_goods','shop_goods.goods_id','=','shop_cart.goods_id')
               ->where($where)
               ->get();
       $data=json_encode($dataInfo);
       return $data;
   }

    /**
     * 从购物车移除商品
     * @param Request $request
     * @return false|string
     */
   public function cartDel(Request $request){
        $goodsId=$request->input('goods_id');
        $user_id=$request->input('user_id');
        $goods_id=explode(',',$goodsId);
        if(empty($goodsId)){
            $res=[
                'code'=>40010,
                'msg'=>'至少选中一件商品'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
        $where=[
            'user_id'=>$user_id
        ];
        $updateInfo=[
            'status'=>2
        ];
        $arr=CartModel::where($where)->whereIn('goods_id',$goods_id)->update($updateInfo);
        if($arr){
            $res=[
                'code'=>200,
                'msg'=>'移除成功'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }else{
            $res=[
                'code'=>40020,
                'msg'=>'移除失败'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
   }

    /**
     * 去结算
     * @param Request $request
     */
   public function pay(Request $request){
       $goods_id=$request->input('goods_id');
       $user_id=$request->input('user_id');
       $goods_id=rtrim($goods_id,',');
       $goodsId=explode(',',$goods_id);
        $where=[
           'user_id'=>$user_id
       ];
       if(empty($goods_id)){
           $res=[
               'code'=>40022,
               'msg'=>'至少选中一件商品'
           ];
           return json_encode($res,JSON_UNESCAPED_UNICODE);
       }
//       die;
       $order_amount=200;
       $orderno=date('YmdHis',time()).rand(1000,9999);
       //添加订单表
        $dataInfo=[
            'order_no'=>$orderno,
            'user_id'=>$user_id,
            'order_amount'=>$order_amount
        ];
        $order_id=DB::table('shop_order')->insertGetId($dataInfo);
       //订单详情入库

       //两表连查订单详情入库
       $data=DB::table('shop_goods')
           ->join('shop_cart','shop_goods.goods_id','=','shop_cart.goods_id')
           ->whereIn('shop_goods.goods_id',$goodsId)
           ->get();
//       var_dump($data);die;
        $info=[];
       foreach($data as $v){
           $arr=[
               'order_id'=>$order_id,
               'order_no'=>$orderno,
               'goods_id'=>$v->goods_id,
               'user_id'=>$user_id,
               'goods_name'=>$v->goods_name,
               'buy_num'=>$v->buy_num,
               'goods_price'=>$v->goods_price,
               'ctime'=>time(),
           ];
           $info[]=$arr;
       }
       $res=Db::table('shop_order_detail')->insert($info);
       $arr=DB::table('shop_cart')->whereIn('goods_id',$goodsId)->where('user_id',$user_id)
           ->update(['buy_num'=>0,'status'=>2]);
        if($arr){
            $res=[
                'code'=>200,
                'orderno'=>$orderno
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }

   }

    /**
     * 订单列表
     * @param Request $request
     */
   public function payShow(Request $request){
       $user_id=$request->input('user_id');
       $orderno=$request->input('orderno');
       $where=[
           'user_id'=>$user_id,
           'order_no'=>$orderno
       ];
       $dataInfo=DB::table('shop_order_detail')->where($where)->get();
       $data=json_encode($dataInfo);
       return $data;
   }
}