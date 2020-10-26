<?php
namespace app\admin\controller;

use app\BaseController;
use thans\layuiAdmin\facade\AdminsAuth;
use thans\layuiAdmin\facade\Json;
use thans\layuiAdmin\facade\Utils;
use thans\layuiAdmin\Form;
use thans\layuiAdmin\model\AuthPermission;
use thans\layuiAdmin\model\Menu as MenuModel;
use thans\layuiAdmin\Table;
use thans\layuiAdmin\Traits\FormActions;
use think\Request;

use think\Controller;
use think\Config;
use think\Cache;
use think\Paginator;
use think\facade\View;
use think\Facade\Db;

use app\admin\model\CustomerModel;
use app\admin\model\CustomerOrdersModel;
use app\admin\model\CustomerOrdersGoodsModel;
use app\admin\model\CustomerOrdersGoodsTemporaryModel;
use app\admin\model\GoodsModel;

class CustomerOrders extends BaseController
{

    public $model;
    public $goods_model;
	public $customer_model;
    public $order_type;
    public $express_type;
	public function initialize()
    {
        $this->model = new CustomerOrdersModel();
        $this->customer_model = new CustomerModel();
        $this->order_goods_model = new CustomerOrdersGoodsModel();
        $this->order_goods_temporary_model = new CustomerOrdersGoodsTemporaryModel();
        $this->goods_model = new GoodsModel();
        $this->order_type = [
            1 => '首单',
            2 => '复购',
            3 => '即时子订单',
            4 => '历史子订单',
            5 => '复购子订单',
            6 => '售后',
        ];
        $this->express_type = [
            1 => '京东',
            2 => '自己送货',
            3 => '德邦',
            4 => '中通',
            5 => '宅急送',
            6 => '邮政平邮',
            7 => '圆通',
            8 => '韵达',
            9 => '申通',
            10 => '顺丰',
            11 => 'EMS',
        ];
    }


    public function index(Request $request)
    {
    	return View::fetch('admin\customer_orders\lists');
    }


    /**
     * 获取列表
     * @throws \think\Exception
     */
    public function lists(Request $request){
    	$params = $request->param();
        // $customer = Db::name('customer')->field('id,customer_name')->where('customer_status',1)->select()->toArray();
        $country = Db::name('category_type')->field('id,name')->where('status',1)->where('type','country')->select()->toArray();
        $order_type = Db::name('category_type')->field('id,name')->where('status',1)->where('type','order_type')->select()->toArray();
        $express_type = Db::name('category_type')->field('id,name')->where('status',1)->where('type','express_type')->select()->toArray();
        // $customer = array_column($customer,'customer_name','id');
        $country = array_column($country,'name','id');
        $order_type = array_column($order_type,'name','id');
        $express_type = array_column($express_type,'name','id');
        $list = $this->model->getListByCond($params);
        $list = $list->withAttr('order_type_id', function($value, $data) use($order_type) {
            return $order_type[$value];
        });
        $list = $list->withAttr('express_type_id', function($value, $data) use($express_type) {
            return $express_type[$value];
        });
        $list = $list->withAttr('country_id', function($value, $data) use($country) {
            return $country[$value];
        });
        $list = $list->withAttr('booking_time', function($value, $data) {
            return date("Y-m-d H:i:s",$value);
        });
        $list = $list->withAttr('fans_time', function($value, $data) {
            return date("Y-m-d H:i:s",$value);
        });

        $list = $list->withAttr('reviewer_status', function($value, $data) {
            $reviewer_status = [ 0 => '未审核', 1 => '已审核' ];
            return $reviewer_status[$value];
        });

        $data = [
            'code' => 0,
            'msg' => '',
            'tiao' => 1,
            'count' => $list->total(),
            'data' => $list->items()
        ];
        return json($data);
    }



    /*
     * 添加页面
     *  */
    public function create(Request $request)
    {
        $method = strtolower($request->method());
        switch ($method){
            case "post":
                $params = $request->param();
                $customer_id = trim($request->param('customer_id'));
                $customer = $this->customer_model->where('id',$customer_id)->where('customer_status',1)->find();
                if(empty($customer)){
                    return Json(['code' => 1001, 'msg' => '客户不存在,请填正确的客户id'], 256);
                }
                

                $customer_no = $this->gen_recharge_sn();
                $order_type_id = trim($request->param('order_type_id'));
                $order_type_explain = trim($request->param('order_type_explain'));
                $express_type_id = trim($request->param('express_type_id'));
                $country_id = trim($request->param('country_id'));
                $express_type_id = trim($request->param('express_type_id'));
                $city_picker = explode('/',$request->param('city_picker'));
                $province = trim($city_picker[0]);
                $city = trim($city_picker[1]);
                $district = trim($city_picker[2]);
                $province_id = trim($request->param('provinceId'));
                $city_id = trim($request->param('cityId'));
                $district_id = trim($request->param('districtId'));
                $address = trim($request->param('address'));
                $sales_wechat = trim($request->param('sales_wechat'));
                $customer_wechat = trim($request->param('customer_wechat'));
                $description = trim($request->param('description'));
                $booking_time = trim($request->param('booking_time'));
                $fans_time = trim($request->param('fans_time'));
                $order_amount = trim($request->param('order_amount'));
                $freight = trim($request->param('freight'));
                $discount_amount = trim($request->param('discount_amount'));
                $payment_amount = trim($request->param('payment_amount'));
                $beforehand_amount = trim($request->param('beforehand_amount'));
                $beforehand_amount_type = trim($request->param('beforehand_amount_type'));
                $collection_amount = trim($request->param('collection_amount'));
                $discoun_card = trim($request->param('discoun_card'));

                $inData = [
                    "customer_no" => $customer_no,
                    "customer_id" => $customer_id,
                    "customer_name" => $customer['customer_name'],
                    "order_type_id" => $order_type_id,
                    "order_type_explain" => $order_type_explain,
                    "express_type_id" => $express_type_id,
                    "country_id" => $country_id,
                    "province" => $province,
                    "city" => $city,
                    "district" => $district,
                    "province_id" => $province_id,
                    "city_id" => $city_id,
                    "district_id" => $district_id,
                    "address" => $address,
                    "sales_wechat" => $sales_wechat,
                    "customer_wechat" => $customer_wechat,
                    "description" => $description,
                    "booking_time" => strtotime($booking_time),
                    "fans_time" => strtotime($fans_time),
                    "order_amount" => $order_amount,
                    "freight" => $freight,
                    "discount_amount" => $discount_amount,
                    "payment_amount" => $payment_amount,
                    "beforehand_amount" => $beforehand_amount,
                    "beforehand_amount_type" => $beforehand_amount_type,
                    "collection_amount" => $collection_amount,
                    "discoun_card" => $discoun_card,
                    "create_time" => time(),
                    "update_time" => time(),
                    'order_status' =>1,
                    'reviewer_status' => 0,
                    'creator_id'    => $this->adminsInfo->id,
                    'creator_name'    => $this->adminsInfo->nickname,
                ];


                $where = [
                    'user_id' => $this->adminsInfo->id,
                    'status' => 1,
                ];
                $order_goods_temporary = $this->order_goods_temporary_model->field('goods_id,user_id,goods_name,goods_small_name,goods_no,spec_id,spec,unit_id,unit_uame,category_id,category_name,unit_price,shipment_quantity,order_quantity,sales_amount,discount_amount,payment_amount,status')->where($where)->select()->toArray(); 

                $resl = 1;
                // 启动事务
                Db::startTrans();
                try {
                    $this->model->insertGetId($inData);
                    $this->order_goods_model->saveAll($order_goods_temporary);
                    $this->order_goods_temporary_model->where($where)->delete(); 

                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    $resl = 0;
                    // 回滚事务
                    Db::rollback();
                }
                if($resl) {
                    return Json(['code'=>1000, 'msg'=> '添加成功'],256);
                }else{
                    return Json(['code' => -1001, 'msg' => '添加失败'],256);
                    exit;
                }
                break;

            case "get":
                $customer = Db::name('customer')->field('id,customer_name')->where('customer_status',1)->select();
                $country = Db::name('category_type')->field('id,name')->where('status',1)->where('type','country')->select();
                $order_type = Db::name('category_type')->field('id,name')->where('status',1)->where('type','order_type')->select();
                $express_type = Db::name('category_type')->field('id,name')->where('status',1)->where('type','express_type')->select();
                $beforehand_amount_type = Db::name('category_type')->field('id,name')->where('status',1)->where('type','beforehand_amount_type')->select();
                $res = [
                    'customer'=>$customer,
                    'country'=>$country,
                    'order_type'=>$order_type,
                    'express_type'=>$express_type,
                    'beforehand_amount_type'=>$beforehand_amount_type,
                ];

                View::assign('data',$res);
                return View::fetch('admin\customer_orders\add');

                $this->assign('store', $store);
                return $this->fetch('add');
                // $this->assign('data',$res);
                // return $this->fetch();
                break;
            default:
                break;
        }
    }



    /*
     * 获取所有临时商品
     */
    public function get_temporary_goods(Request $request){
        $params = $request->param();
        $params['user_id'] = $this->adminsInfo->id;
        $list = $this->order_goods_temporary_model->getListByCond($params);
        $data = [
            'code' => 0,
            'msg' => '',
            'tiao' => 1,
            'count' => $list->total(),
            'data' => $list->items()
        ];
        return json($data);
    }


    /*
     * 获取所有商品
     */
    public function get_all_goods(Request $request){
        $params = $request->param();
        $list = $this->goods_model->getListByCond($params);
        $data = [
            'code' => 0,
            'msg' => '',
            'tiao' => 1,
            'count' => $list->total(),
            'data' => $list->items()
        ];
        return json($data);
    }



    /*
     * 添加临时商品
     */
    public function add_goods(Request $request){
        $params = $request->param();
        $goods = $this->goods_model->where('id',$params['goods_id'])->find()->toArray();
        $insert = [
            'goods_id' => $goods['id'],
            'goods_name' => $goods['goods_name'],
            'goods_small_name' => $goods['goods_small_name'],
            'goods_no' => $goods['goods_no'],
            'spec_id' => $goods['spec_id'],
            'spec' => $goods['spec'],
            'unit_id' => $goods['unit_id'],
            'unit_uame' => $goods['unit_uame'],
            'category_id' => $goods['category_id'],
            'category_name' => $goods['category_name'],
            'unit_price' => $goods['price'],
            'shipment_quantity' => 1,
            'order_quantity' => 1,
            'sales_amount' => $goods['price'],
            'discount_amount' => 0,
            'payment_amount' => $goods['price'],
            'create_time' => time(),
            'update_time' => time(),
            'status' => 1,
            'user_id' => $this->adminsInfo->id,
        ];
        $list = $this->order_goods_temporary_model->insert($insert);

        $data = [
            'code' => 100
        ];
        return json($data);
    }



    /*
     * 删除临时商品
     */
    public function del_goods(Request $request){
        $params = $request->param();
        $list = $this->order_goods_temporary_model->where(['id'=>$params['temporary_goods_id']])->delete();

        $data = [
            'code' => 100
        ];
        return json($data);
    }



    /*
     * 编辑客户下单
     */
    public function show_edit(Request $request){
        $params = $request->param();
        $list = $this->model->where(['id'=>$params['customer_orders_id']])->find();

        $data = [
            'code' => 100
        ];
        return json($data);
    }


    /**
     *   生成编号
     */
    private function gen_recharge_sn()
    {
        /* 选择一个随机的方案 */
        $timestamp = time();
        // $y = date('YmdHis', $timestamp);;
        // $z = date('z', $timestamp);
        $section_no = $timestamp . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        // $order_sn = "200".$y .str_pad($z, 3, '0', STR_PAD_LEFT). str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $section     = $this->model->where('customer_no', $section_no)->find();
        if (empty($section))
        {
            /* 否则就使用这个配置号 */
            return $section_no;
        }
        /* 如果有重复的，则重新生成 */
        return $this->gen_recharge_sn();
    }
}
