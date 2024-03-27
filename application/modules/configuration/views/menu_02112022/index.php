<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent . '">' . $parent; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-hover" id="grid">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/themes/metro/easyui.css" rel="stylesheet" type="text/css">
<style type="text/css">
    .datagrid-header-row {
      height: 35px;
      color: #fff;
    }

    .datagrid-row {
      height: 30px;
    }

    .datagrid-toolbar {
        background: #fff;
        padding-right: 5px !important;
    }

    .datagrid-header-inner {
        float: left;
        width: 100%;
        background-color: #3c8dbc;
    }

    .datagrid-header td.datagrid-header-over {
        background: #204d74;
        color: #fff;
        cursor: default;
    }

    .tree-title {
        font-size: 14px;
        display: inline-block;
        text-decoration: none;
        vertical-align: top;
        white-space: nowrap;
        padding: 0 2px;
        height: 18px;
        line-height: 18px;
    }
</style>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
     $(document).ready(function() {
        $('#grid').treegrid({
            url         : '<?php echo site_url() ?>configuration/menu/get_list',
            method      : 'POST',
            striped     : false,
            fitColumns  : true,
            treeField   : 'name',
            idField     : 'id',
            emptyMsg    : 'Tidak Ada Data.',
            loadMsg     : 'Memproses, tunggu sebentar.',
            scrollbarSize: 0,
            nowrap      : true,
            sortable    : false,
            singleSelect: true,
            columns:[[
                { field: 'name', title: 'Nama Menu', width: 100},
                { field: 'slug', title: 'URL', width: 80},
                { field: 'action', title: 'Aksi', width: 30,align: 'center'},
            ]],

            onLoadSuccess: function(row, data){
                $('.tree-folder').css('background','none');
                $('.tree-file').css('background','none');

                $('#grid').treegrid('resize')
                $('.sidebar-toggle').click(function(){
                    $('#grid').treegrid('resize');
                });

                $(window).resize(function(){
                    $('#grid').treegrid('resize');
                })

                r = data.rows;
                for(i in r){
                    if(r[i].iconCls != ''){
                        $('.'+r[i].iconCls).html('<i class="'+r[i].iconCls+'"></i>');
                    }
                }
            }
        });
    });
</script>
