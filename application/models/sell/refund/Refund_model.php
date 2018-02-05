<?phpinclude_once(dirname(BASEPATH).'/inherit/BaseModel.php');class Refund_model extends BaseModel{    /**     * @var table     */    protected $table = "t_sell_refund";    protected $pk = "id";    /**     * @fields     */    public $id,$order_id,$order_num,$create_at,$create_user_id,$status,$remark,$total_num;    /**     * Refund_model constructor.     */    function __construct(){        $this->load->model('sell/order/Order_model',"m_order",true);        $this->load->model('sell/order/OrderSpu_model',"m_spu",true);        $this->load->model('sell/order/OrderSku_model',"m_sku",true);        $this->load->model('sell/refund/RefundItem_model',"m_item",true);    }    /**     * @return object     */    static function describe(){        $data = (object)array();        $data->desc = "Refund_model";        $data->name = "Refund_model";        return $data;    }    /**     * @return array     */    static function attributeLabels()    {        return [            "id"=>"ID",            "order_id"=>"销售单ID",            "order_num"=>"退货单号",            "create_at"=>"退货时间",            "create_user_id"=>"退货用户ID",            "status"=>"状态",            "remark"=>"备注",            "total_num"=>'退货数量',        ];    }    /**     * 生成销售单号     */    public function createOrderNum(){        list($t1, $t2) = explode(' ', microtime());        $time = (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);        return "HXR".$time;    }    /**     * 添加订单(事务)     */    public function createOrder($data){        //开始事务        $this->db->trans_strict(FALSE);        $this->db->trans_begin();        //执行        try {            //获取最新的配货列表(防止并发)            $order = $this->m_order->get($data["order_id"]);            //$canlist = $order->getSkuCanAllocate();            //保存订单            $this->load($data);            var_dump($data);            die;            $this->save();            //遍历列表            if (!empty($data["list"]) && is_array($data["list"])) {                foreach ($data["list"] as $item) {                    /*if($item["num"] > (int)($canlist[$item["sku_id"]])) {                        throw new Exception("超过配货数量");                    }*/                    $item_model = $this->m_item->_new();                    $item_model->load($item);                    $item_model->refund_id = $this->id;                    $item_model->save();                }            }            //提交并返回            $this->db->trans_commit();            return true;        }catch (Exception $e){            //回滚并返回            $this->db->trans_rollback();            return false;        }    }    /**     * 添加订单(事务)     */    public function updateOrder($data){        //开始事务        $this->db->trans_strict(FALSE);        $this->db->trans_begin();        //执行        try{            //获取最新的配货列表(防止并发)            $allocate = $this->model->get($data["id"]);            $canlist = $allocate->getSkuCanAllocate();            //保存订单            $order = $this->get($data["id"]);            $order->load_safe($data);            $order->save();            //遍历列表            if(!empty($data["list"]) && is_array($data["list"])) {                foreach ($data["list"] as $item) {                    if($item["num"] > (int)($canlist[$item["sku_id"]])) {                        throw new Exception("超过配货数量");                    }                    $item_model = $this->m_item->_new();                    $item_model->load_safe($item);                    $item_model->save();                }            }            //提交并返回            $this->db->trans_commit();            return true;        }catch (Exception $e){            //回滚并返回            $this->db->trans_rollback();            return false;        }    }    /**     * 保存前执行     */    protected function beforeSave()    {        //设置时区        date_default_timezone_set('Asia/Shanghai');        //如果是新增        if(empty($this->id)){            $this->status = 0;            $this->create_at = time();            $this->create_user_id = $this->session->uid;        }        //父类方法        parent::beforeSave(); // TODO: Change the autogenerated stub    }    /**     * 获取Sku列表(包含已经配货数量)     * bool $getEndNum：是否查询已配货数量     * bool $filterSelf：是否过滤掉自己的配货数量     */    public function getSkuList($getEndNum=true,$filterSelf=true){        //获取所有项目        $refund_items = $this->m_item->searchAll(["refund_id"=>$this->id])->list;        //获取销售单SKU信息        $order_items = $this->m_sku->searchAll(["order_id"=>$this->order_id])->list;        //获取已配数量        if($getEndNum)            $refunded = $this->m_item->getRefundStatus($this->order_id,$filterSelf?$this->id:null);        //组装列表        $list = array();        foreach($order_items as $iorder){            foreach($refund_items as $iallocate){                if($iorder->id == $iallocate->order_sku_id){                    $item = $iallocate;                    //拼凑显示信息                    $item->size = $iorder->size;                    $item->color = $iorder->color;                    //SPU内容                    $item->spu = (object)array(                        "order_id"=>$item->order_id,                        "order_spu_id"=>$item->order_spu_id,                        "spu_id"=>$item->spu_id,                    );                    //SKU内容                    $item->sku = (object)array(                        "order_id"=>$item->order_id,                        "order_sku_id"=>$item->order_sku_id,                        "sku_id"=>$item->sku_id,                        "size" => $iorder->size,                        "color" => $iorder->color                    );                    //总数量                    $item->num_sum = $iorder->num;                    //设置已经配置数量                    if($getEndNum)                        $item->num_end = isset($refunded[$iallocate->order_sku_id])?(int)$refunded[$iallocate->order_sku_id]:0;                    //添加到列表                    $list[] = $item;                }            }        }        //返回        return $list;    }}?>    