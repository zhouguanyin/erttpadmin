<?php
namespace app\admin\model;


class CustomerOrdersModel extends BaseModel
{
    protected $name = 'customer_orders';

    /**
     * 获取列表
     * Author Furion
     * Time 2020-10-23
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public static function getListByCond($params)
    {
        extract($params);
        $where = [];
        $page = empty($page) ? '' : $page;
        $limit = empty($limit) ? 10 : $limit;
        if(!empty($key['title'])){
            $where['title'] = ['like','%'.$key['title'].'%'];
        }
        if(!empty($key['nums'])){
            $where['nums'] = ['like','%'.$key['nums'].'%'];
        }
        if(!empty($key['status'])){
            $where['status'] = $key['status'];
        }
        if(!empty($key['start_time'])){
            $dui_time = explode(' - ', $key['start_time']);
            /*$where['start_time'] = $dui_time[0];
            $where['end_time'] = $dui_time[1];*/
            $where['start_time'] = ['BETWEEN',[$dui_time[0].' 00:00:00',$dui_time[1].' 23:59:59']];
        }
        if(!empty($key['end_time'])){
            $dui_time = explode(' - ', $key['end_time']);
            /*$where['start_time'] = $dui_time[0];
            $where['end_time'] = $dui_time[1];*/
            $where['end_time'] = ['BETWEEN',[$dui_time[0].' 00:00:00',$dui_time[1].' 23:59:59']];
        }
        // $where['is_delete'] = 0;
        return self::where($where)
            ->order('id', 'desc')
            ->paginate($limit, false, ['page' => $page,'query'=>[]]);
    }
}