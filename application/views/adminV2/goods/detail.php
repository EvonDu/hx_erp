<?php $this->load->view('adminV2/head'); ?>
    <!-- 内容区域 -->
    <div class="tpl-content-wrapper">
        <div class="row-content am-cf">
            <div class="row am-cf">
                <div class="am-u-sm-12">
                    <div class="widget am-cf">
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">商品管理</div>
                            <div class="widget-function am-fr">
                                <a href="javascript:;" class="am-icon-cog"></a>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-12 am-u-lg-8 widget-margin-bottom-lg">
                            <!--正文-->
                            <style>
                                .am-table > tbody > tr > td {
                                    vertical-align: middle;
                                }

                                .pic {
                                    width: 80px;
                                    height: 100px;
                                }

                                .am-btn-group {
                                    vertical-align: baseline;
                                }
                            </style>
                            <!--功能选项-->
                            <select data-am-selected id="method_select" class="am-u-sm-3" title="选择功能">
                                <option value="get_goods">商品管理</option>
                                <option value="get_category">分类管理</option>
                                <option value="get_color">颜色管理</option>
                                <option value="get_size">尺码管理</option>
                                <option value="get_shop">店铺管理</option>
                            </select>
                            <div class="am-btn-group am-btn-group-xs">
                                <div class="other-select" id="div-get_goods">
                                    <button type="button" class="am-btn am-btn-default"
                                            onclick="window.location.href='/sku/action_add_sku'"><span
                                            class="am-icon-plus"></span>新建商品
                                    </button>
                                    <button type="button" class="am-btn am-btn-default" onclick="expotr()" id="exprot">
            <span class="am-icon-file-excel-o">
                导出
            </span>
                                    </button>
                                    <button type="button" class="am-btn am-btn-default"
                                            data-am-collapse="{parent: '#accordion', target: '#goods_search'}"><span
                                            class="am-icon-search"></span>
                                    </button>
                                    <div class="am-panel-group" id="accordion">
                                        <div class="am-collapse" id="goods_search">
                                            <hr>
                                            <form class="am-form" id="goods_search_form">

                                                <div class="am-form-group">
                                                    <label class="am-form-label">款号</label>
                                                    <input type="text" id="search_goods_id" name="goods_id"/ >
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-form-label">品牌</label>
                                                    <input type="text" id="search_brand" name="brand" / >
                                                </div>


                                                <div class="am-form-group">
                                                    <label class="am-form-label" for="search_category_id">分类</label>
                                                    <select id="search_category_id" name="category_id">
                                                        <option value="">全部</option>
                                                        <?php foreach ($category_list as $key => $category): ?>
                                                            <option value="<?= $key ?>"><?= $category ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-form-label" for="search_color_id">颜色</label>
                                                    <div class="am-u-sm-12">
                                                        <select id="search_color_id" name="color_id"
                                                                data-am-selected="{searchBox: 1,maxHeight:200}">
                                                            <option value="">全部</option>
                                                            <?php foreach ($color_list as $row): ?>
                                                                <option
                                                                    value="<?= $row['id'] ?>"><?= $row['color_num'] ?> <?= $row['name'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-form-label" for="search_size_id">尺码</label>
                                                    <div class="am-u-sm-12">
                                                        <select id="search_size_id" name="size"
                                                                data-am-selected="{searchBox: 1,maxHeight:200}">
                                                            <option value="">全部</option>
                                                            <?php foreach ($size_list as $row): ?>
                                                                <option
                                                                    value="<?= $row['id'] ?>"><?= $row['size_num'] ?> <?= $row['size_info'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-form-label">成本范围</label>
                                                    <div class="am-u-sm-12">
                                                        <div class="am-u-sm-5 am-padding-left-0">
                                                            <input type="text" name="cost_min">
                                                        </div>
                                                        -
                                                        <div class="am-u-sm-5 am-padding-left-0 am-padding-right-0">
                                                            <input type="text" name="cost_max">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-form-label">价格范围</label>
                                                    <div class="am-u-sm-12">
                                                        <div class="am-u-sm-5 am-padding-left-0">
                                                            <input type="text" name="price_min">
                                                        </div>
                                                        -
                                                        <div class="am-u-sm-5 am-padding-left-0 am-padding-right-0">
                                                            <input type="text" name="price_max">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-form-label">库存范围</label>
                                                    <div class="am-u-sm-12">
                                                        <div class="am-u-sm-5 am-padding-left-0">
                                                            <input type="text" name="stock_min">
                                                        </div>
                                                        -
                                                        <div class="am-u-sm-5 am-padding-left-0 am-padding-right-0">
                                                            <input type="text" name="stock_max">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <div class="am-u-sm-12">
                                                        <button type="button" class="am-btn am-btn-default"
                                                                onclick="search_goods()"><span
                                                                class="am-icon-search"></span>搜索
                                                        </button>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>

                                </div>
                                <div class=" other-select" id="div-get_category">
                                    <button type="button" class="am-btn am-btn-default"
                                            onclick="window.location.href='/category/action_add_category'"><span
                                            class="am-icon-plus"></span>添加分类
                                    </button>
                                </div>
                                <div class="other-select" id="div-get_color">
                                    <button type="button" class="am-btn am-btn-default"
                                            onclick="window.location.href='/color/action_add_color'"><span
                                            class="am-icon-plus"></span>添加颜色
                                    </button>
                                </div>
                                <div class="other-select" id="div-get_size">
                                    <button type="button" class="am-btn am-btn-default"
                                            onclick="window.location.href='/size/action_add_size'"><span
                                            class="am-icon-plus"></span>添加尺码
                                    </button>
                                </div>
                                <div class="other-select" id="div-get_shop">
                                    <button type="button" class="am-btn am-btn-default"
                                            onclick="window.location.href='/shop/action_add_shop'"><span
                                            class="am-icon-plus"></span>添加店铺
                                    </button>
                                </div>
                            </div>
                            <!--表格子-->
                            <table class="am-table" id="from_table">
                                <thead id="from_thead" class="am-text-nowrap">
                                </thead>
                                <tbody id="from_contant">
                                </tbody>
                            </table>
                            <br>

                            <!--分页-->
                            <div id="page">
                            </div>


                            <div class="am-modal am-modal-confirm" tabindex="-1" id="color-remove-confirm">
                                <div class="am-modal-dialog">
                                    <div class="am-modal-hd">提示</div>
                                    <div class="am-modal-bd">
                                        确定要删除这条记录吗？
                                    </div>
                                    <div class="am-modal-footer">
                                        <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                                        <span class="am-modal-btn" data-am-modal-confirm>确定</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-modal am-modal-confirm" tabindex="-1" id="size-remove-confirm">
                                <div class="am-modal-dialog">
                                    <div class="am-modal-hd">提示</div>
                                    <div class="am-modal-bd">
                                        确定要删除这条记录吗？
                                    </div>
                                    <div class="am-modal-footer">
                                        <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                                        <span class="am-modal-btn" data-am-modal-confirm>确定</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-modal am-modal-confirm" tabindex="-1" id="goods-remove-confirm">
                                <div class="am-modal-dialog">
                                    <div class="am-modal-hd">提示</div>
                                    <div class="am-modal-bd">
                                        确定要删除这条记录吗？
                                    </div>
                                    <div class="am-modal-footer">
                                        <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                                        <span class="am-modal-btn" data-am-modal-confirm>确定</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-modal am-modal-confirm" tabindex="-1" id="shop-remove-confirm">
                                <div class="am-modal-dialog">
                                    <div class="am-modal-hd">提示</div>
                                    <div class="am-modal-bd">
                                        确定要删除这条记录吗？
                                    </div>
                                    <div class="am-modal-footer">
                                        <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                                        <span class="am-modal-btn" data-am-modal-confirm>确定</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-modal am-modal-confirm" tabindex="-1" id="sell-off">
                                <div class="am-modal-dialog">
                                    <div class="am-modal-hd">提示</div>
                                    <div class="am-modal-bd">
                                        确定要下架吗？
                                    </div>
                                    <div class="am-modal-footer">
                                        <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                                        <span class="am-modal-btn" data-am-modal-confirm>确定</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-modal am-modal-confirm" tabindex="-1" id="sell-on">
                                <div class="am-modal-dialog">
                                    <div class="am-modal-hd">提示</div>
                                    <div class="am-modal-bd">
                                        确定要上架吗？
                                    </div>
                                    <div class="am-modal-footer">
                                        <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                                        <span class="am-modal-btn" data-am-modal-confirm>确定</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('adminV2/foot'); ?>
<script type="text/javascript" src="/assets/js/amazeui.page.js"></script>
<script type="text/javascript" src="/assets/js/common/from.js"></script>
<script type="text/javascript" src="/assets/js/goodsjs.js"></script>
