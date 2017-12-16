<?php
include_once(dirname(BASEPATH).'/inherit/BaseModel.php');
class Order_model extends BaseModel{
	/**
	 * @var table
	 */
	protected $table = "t_sell_order";
	protected $pk = "id";

	/**
	 * @fields
	 */
	public $id,$order_num,$user_id,$client_id,$total_num,$total_price,$payment,$status,$remark,$create_at,$update_at,$create_user_id,$update_user_id;
	public $total_amount,$delivery_type,$delivery_addr,$receipt_date,$remark_images;
	public $isPrinted,$isReceipted;
	public $allocate_mode;

	/**
	 * Order_model constructor.
	 */
	function __construct(){
		$this->load->model('sell/order/OrderSpu_model',"MSpu",true);
		$this->load->model('sell/order/OrderSku_model',"MSku",true);
		$this->load->model('sell/allocate/AllocateItem_model',"m_item",true);
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
			$this->isPrinted = false;
		}

		//设置打印状态
		if((int)$this->isPrinted == 1)
			$this->isPrinted = true;
		else
			$this->isPrinted = false;

		//设置支付状态
		if(empty($this->receipt_date))
			$this->isReceipted = false;
		else
			$this->isReceipted = true;

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
	 * 获取配货类型名
	 */
	public function getAllocateModeName(){
		$list = $this->getAllocateModeMap();
		$name = $list[$this->allocate_type];
		return $name;
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
	 * 获取配货类型表
	 */
	public function getAllocateModeMap(){
		return [
			0=> "默认",
			1=> "定制",
			2=> "齐了配",
			3=> "单款配齐",
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
	public function updateOrder($data){
		//开始事务
		$this->db->trans_strict(FALSE);
		$this->db->trans_begin();

		//保存订单
		$this->load($data);
		$this->save();

		//删除所有旧项
		$this->MSpu->deleteAll(["order_id" => $this->id]);
		$this->MSku->deleteAll(["order_id" => $this->id]);

		//遍历spu
		foreach($data["selectList"] as $spu_data){
			//去除多余字段
			unset($spu_data["filter"]);
			//保存SPU
			$spu = $this->MSpu->_new();
			$spu->load_safe($spu_data);
			$spu->order_id = $this->id;
			$spu->save();
			//遍历sku
			foreach($spu_data["skus"] as $sku_data){
				//去除多余字段
				unset($sku_data["num_allocat"]);
				//保存SPU
				$sku = $this->MSku->_new();
				$sku->load_safe($sku_data);
				$sku->order_id = $this->id;
				$sku->order_spu_id = $spu->id;
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

	/**
	 * 获取处理好的商列表
	 * @return array
	 */
	public function getGoods(){
		$spus = $this->MSpu->searchAll(['order_id'=>$this->id]);
		$skus = $this->MSku->searchAll(['order_id'=>$this->id]);
		$allocated = $this->m_item->getAllocateStatus($this->id);
		$list = array();
		foreach($spus->list as $spu){
			$item = $spu;
			$item->skus = array();
			foreach($skus->list as $sku){
				//添加配货完成数量
				if($sku->order_spu_id == $spu->id){
					//添加配货完成数量
					foreach($allocated as $key=>$value){
						if($key == $sku->id)
							$sku->num_allocat = $value;
					}
					if(!isset($sku->num_allocat))
						$sku->num_allocat = '0';
					//添加sku
					$spu->skus[] = $sku;
				}
			}
			$list[] = $item;
		}
		return $list;
	}

	/**
	 * 修改状态
	 */
	public function changeStatus($value,$allowBack = false){
		if(!$allowBack && (int)$this->status <= (int)$value)
			$this->status = $value;
		else if($allowBack)
			$this->status = $value;
	}

	/**
	 * 获取Sku列表(包含已经配货数量)
	 * bool $getEndNum：是否查询已配货数量
	 * string $filterAllocatId：统计配货数量时，过滤掉得配货单ID
	 */
	public function getSkuList($getEndNum=true,$filterAllocatId=null){
		//获取所有spu
		$order_spus = $this->m_spu->searchAll(["order_id"=>$this->id])->list;
		$order_skus = $this->m_sku->searchAll(["order_id"=>$this->id])->list;

		//获取已配数量
		if($getEndNum)
			$allocated = $this->m_item->getAllocateStatus($this->id,$filterAllocatId);

		//获取
		$list = array();
		foreach($order_spus as $order_spu){
			foreach($order_skus as $order_sku){
				//过滤
				if($order_spu->id != $order_sku->order_spu_id)
					continue;

				//设置项目
				$item = $this->m_item->_new();
				$item->order_id = $this->id;
				$item->order_spu_id = $order_spu->id;
				$item->order_sku_id = $order_sku->id;
				$item->spu_id = $order_spu->spu_id;
				$item->sku_id = $order_sku->sku_id;
				$item->num = 0;
				$item->status = 0;
				$item->spu = $order_spu;
				$item->sku = $order_sku;

				//设置可配数量
				$item->num_sum = (int)$order_sku->num;

				//设置已经配置数量
				if($getEndNum)
					$item->num_end = isset($allocated[$order_sku->id])?(int)$allocated[$order_sku->id]:0;

				//添加到列表
				$list[] = $item;
			}
		}


		//返回
		return $list;
	}
}
?>    