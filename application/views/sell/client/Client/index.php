<!-- 面包屑 --><div class="am-cf am-padding am-padding-bottom-0">    <select data-am-selected id="subnav">        <option value="<?=site_url("sell/order/Order")?>">销售订单</option>        <option value="#" selected><a>客户管理</a></option>    </select></div><hr><!-- 搜索条 --><div class="am-g">    <div class="am-u-sm-12">        <form>            <div class="am-input-group" style="margin: 12px 0px">                <span class="am-input-group-label"><i class="am-icon-search" aria-hidden="true"></i></span>                <input id="search" name="key" type="text" class="am-form-field">                <span class="am-input-group-label" onclick="search()">搜索</span>            </div>        </form>    </div></div><!-- 动作按钮 --><div class="am-g">    <div class="am-u-sm-12 am-u-md-6">        <div class="am-btn-toolbar">            <div class="am-btn-group am-btn-group-xs">                <a type="button" class="am-btn am-btn-default"  href="<?=UrlComponent::create($_controller)?>"><span class="am-icon-plus"></span> 新增</a>            </div>        </div>    </div></div><!-- 列表内容 --><div class="am-g">    <div class="am-u-sm-12">        <!-- 表单 -->        <form class="am-form">            <!--DataGrid-->            <?=ViewComponent::DataGrid($_controller,$searched,[                'id','name','phone'/*,'addr',*/,'delivery_addr'            ])?>            <!--分页条-->            <?=ViewComponent::PagesBar($page,$size,$searched->count)?>            <hr />        </form>    </div></div><!-- 查询脚本 --><script>    $(function(){        $key = getQueryString("key");        $("#search").val($key);    })    function search(){        var key = $("#search").val();        window.location.href ="?key="+key;    }    function getQueryString(name) {        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");        var r = window.location.search.substr(1).match(reg);        if (r != null) return decodeURI(r[2]); return null;    }</script>