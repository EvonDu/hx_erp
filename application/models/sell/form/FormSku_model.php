<?phpinclude_once(dirname(BASEPATH).'/inherit/BaseModel.php');class FormSku_model extends BaseModel{ /**  * @var table  */ protected $table = "t_sell_form_sku"; protected $pk = "id"; /**  * @fields  */ public $id,$form_id,$fspu_id,$sku_id,$color,$size,$num;}?>    