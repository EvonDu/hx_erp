<?phpdefined('BASEPATH') OR exit('No direct script access allowed');include_once(dirname(BASEPATH).'/inherit/BaseController.php');class Order extends BaseController {    /**     * constructor.     */    function __construct()    {        //父类        parent::__construct();        //环境变量设置        $this->_controller->api = "sell/Api";        $this->_controller->views = "sell/order/Order";        $this->_controller->controller = "sell/order/Order";        $this->_controller->layout = "layout/amaze/hx";        //类库        $this->load->library('evon/ApiResult','','apiresult');        //加载模型        $this->load->model('sell/order/Order_model',"model",true);        $this->load->model('sell/client/Client_model',"m_client",true);        $this->load->model('sell/order/OrderSpu_model',"m_spu",true);        $this->load->model('sell/order/OrderSku_model',"m_sku",true);        $this->load->model('goods/Goods_model',"m_goods",true);        $this->load->model('goods/Sku_model',"m_good_sku",true);        $this->load->model('admin/User_model',"m_user",true);        $this->load->model('goods/Shop_model',"m_shop",true);    }    /**     * index     */    public function index()    {        //设置模型        $model = $this->model;        //获取状态列表        $statusMap = $model->getStatusMap();        //调用视图        $this->show("list",[            "statusMap"=>$statusMap,        ]);    }    /**     * index     */    public function index2()    {        $model = $this->model;        $param = $_REQUEST;        $page = isset($param["page"])?$param["page"]:1;        $size = isset($param["size"])?$param["size"]:20;        $condition = isset($param["condition"])?(array)json_decode($param["condition"]):[];        $sort = isset($param["sort"])?(array)json_decode($param["sort"]):[$model->getPk()=>"ASC"];        $result = $model->search($page,$size,$condition,$sort);        $this->show("index",[            "searched"=>$result,            "page"=>$page,            "size"=>$size,        ]);    }    /**     * create     */    public function create(){        $model = $this->model;        $param = $_REQUEST;        if (!empty($param) && $model->load($param) && $model->save()) {            redirect($this->_controller->views."/index");        } else {            $this->show("create",[                "model"=>$model,            ]);        }    }    /**     * update     */    public function update2($id){        $model = $this->model->get($id);        $param = $_REQUEST;        if (!empty($param) && $model->load($param) && $model->save()) {            redirect($this->_controller->views."/index");        } else {            $this->show("update",[                "model"=>$model,            ]);        }    }    /**     * delete     */    public function delete($id){        $model = $this->model->get($id);        $bool = $model->delete();        if($bool)            redirect($this->_controller->views."/index");    }    /**     * view     */    public function view($id){        $model = $this->model->get($id);        var_dump($model);    }    /**     * 添加订单     */    public function add(){        $model = $this->model;        $model->order_num = $this->model->createOrderNum();        $model->payment = 0;        $model->client = null;        $model->goods = [];        $model->allocate_mode = 0;        $paymentMap = $this->model->getPaymentMap();        $deliveryTypeMap = $this->m_client->getDeliveryTypeMap();        $allocateModeMap = $this->model->getAllocateModeMap();        //获取店铺信息        $uid = $this->session->uid;        $user = $this->m_user->get_user_info($uid);        $shop = isset($user["shop_info"][0])?$user["shop_info"][0]:null;        //调用视图        $this->show("order",[            "model"=>$model,            "isNew"=>1,            "spuAllowChange"=>1,            "paymentMap"=>$paymentMap,            "deliveryTypeMap"=>$deliveryTypeMap,            "allocateModeMap"=>$allocateModeMap,            "shop"=>$shop        ]);    }    /**     * 修改订单     */    public function modify($id){        //获取模型信息        $model = $this->model->get($id);        $paymentMap = $this->model->getPaymentMap();        $deliveryTypeMap = $this->m_client->getDeliveryTypeMap();        $allocateModeMap = $this->model->getAllocateModeMap();        //判断是否存在在，并且能更新        if(empty($model->id) || (string)$model->status != "0") {            redirect($this->_controller->views . "/index");        }        //获取客户        $client = empty($model->client_id)?null:$this->m_client->get($model->client_id);        $model->client = $client;        //获取列表        $model->goods = $model->getGoods();        //判断是否允许更改spu        $spuAllowChange = 1;        $create_date = date("Y-m-d",$model->create_at);        $next_time = strtotime($create_date)+86400;        if(time()>$next_time) $spuAllowChange = 0;        //是否为新单(复用spuAllowChange，可以完全替代)        $isNew = $spuAllowChange;        //跳转到视图        $this->show("order",[            "model"=>$model,            "isNew"=>$isNew,            "spuAllowChange"=>$spuAllowChange,            "paymentMap"=>$paymentMap,            "deliveryTypeMap"=>$deliveryTypeMap,            "allocateModeMap"=>$allocateModeMap        ]);    }    /**     * 添加/修改（异步）     */    public function update_asyn(){        //参数检测        $this->apiresult->checkApiParameter(['user_id','client_id',"total_num","total_price","payment","client"],-1);        $param = $_REQUEST;        $param_client = $_REQUEST["client"];        unset($_REQUEST["client"]);        unset($_REQUEST["num_allocat"]);        //保存客户信息        if($param_client){            $client = $this->m_client->get($param_client["id"]);            $client->load($param_client);            $client->save();        }        //设置参数        $param["status"] = 0;        //设置模型        if(isset($param["id"]))            $model = $this->model->get($param["id"]);        else            $model = $this->model;        //事务方式处理表单        $bool = $model->updateOrder($param);        //输出结果        if($bool)            $this->apiresult->sentApiSuccess();        else            $this->apiresult->sentApiError(-1,"fail");    }    /**     * 作废（异步）     */    public function scrap_asyn(){        //参数检测        $this->apiresult->checkApiParameter(['id'],-1);        $id = $_REQUEST["id"];        //获取模型        $model = $this->model->get($id);        //判断并修改状态        if($model->status == 0)        {            $model->status = 3;            if($model->save())                $this->apiresult->sentApiSuccess();            else                $this->apiresult->sentApiError(-1,"fail");        }        else        {            $this->apiresult->sentApiError(-1,"status error");        }    }    /**     * 打印     * @param $id     */    public function printout($id){        //获取配置        $this->config->load('lodop');        $lodop = $this->config->item('lodop');        //获取订单，及商品信息        $model = $this->model->get($id);        $model->goods = $model->getGoods();        //获取客户信息        $client = empty($model->client_id)?null:$this->m_client->get($model->client_id);        //获取销售员信息        $seller = empty($model->user_id)?null:$this->m_user->get_user_info($model->user_id);        //获取店铺信息(直接调用获取店铺)        $shop = null;        if($model->shop_id){            $search = $this->m_shop->shop_detail_by_id($model->shop_id);            if($search["result_rows"])                $shop = $search["result_rows"];        }        //跳转到视图        $this->show("printout",[            "seller"=>$seller,            "client"=>$client,            "order"=>$model,            "lodop"=>$lodop,            "shop"=>$shop,        ]);    }}