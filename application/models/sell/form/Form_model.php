<?php
include_once(dirname(BASEPATH).'/inherit/BaseModel.php');
class Form_model extends BaseModel{
	/**
	 * @var table
	 */
	protected $table = "t_sell_form";
	protected $pk = "id";

	/**
	 * @fields
	 */
	public $id,$order_num,$user_id,$client_id,$total_num,$total_price,$payment,$status,$remark,$create_at,$update_at,$create_user_id,$update_user_id;
	public $total_amount,$delivery_type,$delivery_addr,$receipt_date,$remark_images;

	/**
	 * Form_model constructor.
	 */
	function __construct(){
		$this->load->model('sell/form/FormSpu_model',"MSpu",true);
		$this->load->model('sell/form/FormSku_model',"MSku",true);
	}

	/**
	 * @return array
	 */
	static function attributeLabels()
	{
		return [
			'id' => 'ID',
			'order_num' => '销售单号',
			'user_id' => '销售员ID',
			'client_id' => '客户ID',
			'tatol_num' => '合计数量',
			'total_price' => '合计金额',
			'total_amount'=> '订单金额',
			'payment' => '支付方法',
			'remark' => '备注',
			'status' => '状态',
			'create_at' => '创建时间',
			'update_at' => '更新时间',
			'create_user_id' => '创建人ID',
			'update_user_id' => '更新人ID',
			'delivery_type'=>'收货方式',
			'delivery_addr'=>'收货地址',
			'receipt_date'=>'收款日期',
			'remark_images'=>'图片备注',
		];
	}

	/**
	 * @return object
	 */
	static function describe()
	{
		$data = (object)array();
		$data->desc = "销售订单";
		$data->name = "From";

		return $data;
	}

	/**
	 * 保存前执行
	 */
	protected function beforeSave()
	{
		//设置时区
		date_default_timezone_set('Asia/Shanghai');

		//保存当前时间戳
		$this->update_at = time();
		$this->update_user_id = $this->session->uid;

		//如果是新增
		if(empty($this->id)){
			$this->create_at = time();
			$this->create_user_id = $this->session->uid;
			$this->total_amount = $this->total_price;
		}

		//父类方法
		parent::beforeSave(); // TODO: Change the autogenerated stub
	}

	/**
	 * 获取状态名
	 * @return string
	 */
	public function getStatusName(){
		switch($this->status){
			case 0:
				return "待配货";
			case 1:
				return "配货中";
			case 2:
				return "已配货";
			case 3:
				return "已作废";
			default:
				return "其他";
		}
	}

	/**
	 * 获取状态映射表
	 * @return object
	 */
	public function getStatusMap(){
		return (object)[
			0=> "待配货",
			1=> "配货中",
			2=> "已配货",
			3=> "已作废",
		];
	}

	/**
	 * 支付方式映射
	 */
	public function getPaymentMap(){
		return (object)[
			0=> "默认付款方式",
			1=>"现金",
			2=>"工行汇款",
			3=> "农行汇款",
			4=> "建行汇款",
			5=> "交行汇款",
			6=> "POS机刷卡",
			7=> "其他",
			8=> "微信",
			9=> "未付",
		];
	}

	/**
	 * 添加/修改
	 */
	public function updateForm($data){
		//开始事务
		$this->db->trans_strict(FALSE);
		$this->db->trans_begin();

		//保存订单
		$this->load($data);
		$this->save();

		//删除所有旧项
		$this->MSpu->deleteAll(["form_id" => $this->id]);
		$this->MSku->deleteAll(["form_id" => $this->id]);

		//遍历spu
		foreach($data["selectList"] as $spu_data){
			//去除多余字段
			unset($spu_data["filter"]);
			//保存SKU
			$spu = $this->MSpu->_new();
			$spu->load($spu_data);
			$spu->form_id = $this->id;
			//$spu->spu_id = $spu_data["spu_id"];
			//$spu->snap_price = $spu_data["snap_price"];
			//$spu->snap_pic = $spu_data["snap_pic"];
			//$spu->snap_pic_normal = $spu_data["snap_normal"];
			$spu->save();
			//遍历sku
			foreach($spu_data["skus"] as $sku_data){
				$sku = $this->MSku->_new();
				$sku->load($sku_data);
				$sku->form_id = $this->id;
				$sku->form_spu_id = $spu->id;
				/*$sku->sku_id = $sku_data["sku_id"];
				$sku->color = $sku_data["color"];
				$sku->size = $sku_data["size"];
				$sku->num = $sku_data["num"];*/
				$sku->save();
			}
		}

		//事务结束处理
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}
	}

	/**
	 * 生成销售单号
	 * */
	public function createOrderNum(){
		list($t1, $t2) = explode(' ', microtime());
		$time = (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
		return "HXS".$time;
	}

	/**
	 * 联表模糊查询
	 * @param array $condition
	 * @param array $sort
	 * @return object
	 */
	public function searchLikeJoinAll($searchKey,$condition=[],$sort=[]){
		//按照加官，使用sql前所有字段加F
		$condition = $this->addPrefixKeyValue($condition);
		$sort = $this->addPrefixKeyValue($sort);

		//字段添加表名
		$condition_table = array();
		foreach($condition as $key=>$value){
			$condition_table[$this->table.".".$key] = $value;
		}

		//查找数量
		//$count = $this->db->where($condition)->count_all_results($this->table);
		$count = $this->getCount($condition_table);

		//条件筛选
		$select = $this->db
			->select(array(
				$this->table.".*",
				"client.Fname as Fclient_name",
				"client.Fphone as Fclient_phone",
				"user.Fname as Fseller_name",
			))
			->where($condition_table);

		//模糊条件
		$select = $select->group_start()
			->or_like('client.Fname', $searchKey)
			->or_like('client.Fphone', $searchKey)
			->or_like('user.Fname', $searchKey)
			->or_like($this->table.'.Ftotal_amount', $searchKey)
			->group_end();

		//排序
		foreach($sort as $key=>$value){
			$key = $this->table.".".$key;
			$select = $select->order_by($key,$value);
		}

		//连接表
		$select = $select
			->join('client', $this->table.'.Fclient_id = client.Fid', 'left')
			->join('user', $this->table.'.Fuser_id = user.Fuid', 'left');

		//查询表
		$select = $select->get($this->table);

		//构成返回结果
		$list = array();
		foreach($select->result() as $data){
			$item = $this->_new();
			$item->load((array)$data);
			$list[] = $item;
		}

		//返回
		$result = (object)array();
		$result->list = $list;
		$result->count = $count;
		$result->model = $this;
		return $result;
	}
}
?>    