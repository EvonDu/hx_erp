<?php

/**
 * Created by PhpStorm.
 * User: lujagan
 * Date: 2017/6/25
 * Time: 下午10:17
 */
class Sku_model extends HX_Model
{


    private $table = "t_sku";
    private $table_prefixes = "F";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function sku_delete_by_id($id)
    {
        return $this->db->update($this->table, ['Fstatus' => 0, 'Fop_uid' => $this->session->uid], ['Fid' => $id]);
    }

    public function get_sku_list_by_goods_id($goods_id)
    {
        $s = "SELECT Fsku_id FROM {$this->table} WHERE Fgoods_id = ? AND Fstatus = 1  ORDER BY Fcreate_time DESC";
        $ret = $this->db->query($s, [$goods_id]);
        $sku_list = [];
        foreach ($ret->result('array') as $row) {
            array_push($sku_list, $row['sku_id']);
        }
        return $this->suc_out_put($sku_list);
    }

    public function get_sku_list_info_by_goods_id($goods_id)
    {
        $s = "SELECT * FROM {$this->table} WHERE Fgoods_id = ? AND Fstatus = 1  ORDER BY Fcreate_time DESC";
        $ret = $this->db->query($s, [$goods_id]);
        $sku_list = [];
        foreach ($ret->result('array') as $row) {
            array_push($sku_list, $row);
        }
        return $this->suc_out_put($sku_list);
    }

    public function get_sku_list($page = 1)
    {
        $s = "SELECT * FROM {$this->table} WHERE Fstatus = 1";
        $ret = $this->db->query($s);
        $this->total_num = $ret->num_rows();

        $s = "SELECT * FROM {$this->table} WHERE Fstatus = 1  ORDER BY Fcreate_time DESC LIMIT ? , ?";
        $this->offset = 0;
        $this->limit = 10;
        if ($page < 1) {
            $page = 1;
        }
        $ret = $this->db->query($s, [
            $this->offset + ($page - 1) * $this->limit,
            $this->limit
        ]);

        return $this->suc_out_put($ret->result('array'));
    }

    public function check_product_number_available($request)
    {
        $s = "SELECT * FROM {$this->table} WHERE Fstatus = 1  AND Fproduct_number = ? ;";

        $ret = $this->db->query($s, [
            $request['product_number']
        ]);
        return $this->suc_out_put($ret->row(0, 'array'));
    }

    private function insert_sku_pararm($request)
    {
        $params = [];
        if (isset($request['goods_id']) && isset($request['color']) && isset($request['size'])) {
            $params = $this->get_sku_id($request, $request['color'], $request['size']);
        } else {
            show_error('缺少颜色或尺码');
        }
        return $params;
    }

    private function get_sku_id($request, $color_id_array, $size_id_array)
    {
        $sku_info = [];
        $sku_id_list = $this->get_sku_list_by_goods_id($request['goods_id'])['result_rows'];
        $i = 0;
        foreach ($color_id_array as $k => $color_row) {
            foreach ($size_id_array as $z => $size_row) {

                $curr_sku_id = $request['goods_id'] . 'C' . $color_row . 'S' . $size_row;
                if (in_array($curr_sku_id, $sku_id_list)) {
                    continue;
                }
                $sku_info[$i]['Fsku_id'] = $curr_sku_id;
                $sku_info[$i]['Fgoods_id'] = $request['goods_id'];
                $sku_info[$i]['Fcolor_id'] = $color_row;
                $sku_info[$i]['Fsize_id'] = $size_row;


                if (isset($request['price'])) {
                    $sku_info[$i]['Fprice'] = $request['price'];
                }
                if (isset($request['pic'])) {
                    $sku_info[$i]['Fpic'] = $request['pic'];
                }
                if (isset($request['record_number'])) {
                    $sku_info[$i]['Frecord_number'] = $request['record_number'];
                }
                if (isset($request['brand'])) {
                    $sku_info[$i]['Fbrand'] = $request['brand'];
                }
                if (isset($request['category_id'])) {
                    $sku_info[$i]['Fcategory_id'] = $request['category_id'];
                }
                if (isset($request['category'])) {
                    $sku_info[$i]['Fcategory'] = $request['category'];
                }
                if (isset($request['memo'])) {
                    $sku_info[$i]['Fmemo'] = $request['memo'];
                }
                if (isset($request['op_uid'])) {
                    $sku_info[$i]['Fop_uid'] = $this->session->uid;
                }
                if (isset($request['status'])) {
                    $sku_info[$i]['Fstatus'] = $request['status'];
                } else {
                    $sku_info[$i]['Fstatus'] = 1;
                }
                $i++;
            }
        }
        return $sku_info;
    }

    public function modify_sku($request)
    {
        $params = $this->insert_sku_pararm($request);
        if ($this->reentry) {
            $this->db->update($this->table, $params, ['Fid' => $this->reentry]);
        } else {
            $this->db->insert_batch($this->table, $params);
        }

    }

    public function get_row_by_id($uid)
    {
        $s = "SELECT * FROM {$this->table} u  WHERE u.Fstatus = 1 AND Fid = ? LIMIT 1;";
        $ret = $this->db->query($s, [$uid]);
        return $this->suc_out_put($ret->row(0, 'array'));
    }

    public function get_by_sku_id($sku_id)
    {
        $s = "SELECT * FROM {$this->table} u  WHERE u.Fstatus = 1 AND Fsku_id = ? LIMIT 1;";
        $ret = $this->db->query($s, [$sku_id]);
        return $this->suc_out_put($ret->row(0, 'array'));
    }
}