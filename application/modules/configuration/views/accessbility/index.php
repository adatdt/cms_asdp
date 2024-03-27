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
<div class="page-content-wrapper" id="box">
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
                    <div class="caption">
                        <?php echo $title; ?>
                    </div>
                </div>
                <div class="portlet-body" style="padding-bottom: 50px">
                    <div id="alerts"> </div>
                    <form id="ff" action="<?php echo site_url('configuration/accessbility/action_privilege'); ?>" method="post">



                        <table class="table table-bordered table-hover" id="grid"></table>
                        <br>
                        
                        <?php echo generate_button('configuration/accessbility', 'edit', '<button type="submit" class="btn btn-warning pull-right" id="save" style="display: none">Simpan</button>') ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#access').hide();
        $('#desc').hide();
        $('#save').hide();
        $('#check').hide();
        $('#checkAll').hide();
        $('#group').select2();

        // $('#group').on('select2:selecting', function(e) {
        //     val = e.params.args.data.id;
        //     gridPrivilege(val);
        //     $('#checkAll').iCheck('enable');
        //     $('#checkAll').iCheck('uncheck');
        // });

        gridPrivilege();
        validateForm('#ff',function(url,data){
            // data.actions = $('#menuAction').val();
            // postData(url,data,true);
            act = $(".actions")
            arr = [];
            for (var i = 0; i < act.length; ++i) {
                action = $(act[i])[0];
                p = action.dataset;
                if(action.checked){                    
                    arr[i] = {
                        detail_id: p.menu_detail_id,
                        status_checked:1
                    };
                }
                else
                {
                    arr[i] = {
                        detail_id: p.menu_detail_id,
                        status_checked:0
                    };   
                }
                // console.log(action.checked)
            }
            data.actions = arr;
            // console.log(data);
            postData(url,data,true);

            // console.log($(act))
        });
    });

    function gridPrivilege(){
        // $.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});
        var csfrData = {};
        csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
        $.ajaxSetup({
            data: csfrData
        });
        $('#grid').treegrid({
            url         : '<?php echo site_url() ?>configuration/accessbility/get_list',
            // queryParams : {
            //     group : group_id
            // },
            method      : 'POST',
            striped     : false,
            fitColumns  : true,
            treeField   : 'name',
            idField     : 'id',
            emptyMsg    : 'Tidak Ada Data.',
            loadMsg     : 'Memproses, tunggu sebentar.',
            scrollbarSize: 0,
            nowrap      : false,
            sortable    : false,
            singleSelect: true,
            columns:[[
                { field: 'menu_id', title: '<label style="padding-top: 7px;"><input type="checkbox" id="checkAll" class="checkAll" ></label>', width: 15,align: 'center'},
                // { field: '', title: '', width: 15,align: 'center'},
                { field: 'name', title: 'NAMA MENU', width: 50},
                { field: 'action', title: '<label style="padding-top: 7px;"><input type="checkbox" class="act  checkAll" id="actCloud" ></label> Akses Clode', width: 100},
                { field: 'action_local', title: '<label style="padding-top: 7px;"><input type="checkbox" class="act  checkAll" id="actLocal" ></label> Akses Lokal', width: 100,align: 'left'},
                // { field: 'add', title: 'ADD', width: 20,align: 'center'},
                // { field: 'edit', title: 'EDIT', width: 20,align: 'center'},
                // { field: 'delete', title: 'DELETE', width: 20,align: 'center'},
                // { field: 'detail', title: 'DETAIL', width: 20,align: 'center'},
                // { field: 'approval', title: 'APPROVAL', width: 20,align: 'center'},
            ]],

            onLoadSuccess: function(row, data){
                csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] = data.tokenHash;
                $.ajaxSetup({
                    data: csfrData
                });
                
                // $.unblockUI();
                arr  = [];

                $('#access').show();
                $('#desc').show();
                $('#save').show();
                $('#checkAll').show();
                $('#check').show();

                privilege = data.privilege;

                if(privilege){
                    $('#privilege_name').val(privilege.privilege_name);
                    $('#privilege_desc').val(privilege.privilege_desc)
                }else{
                    $('#privilege_name').val('');
                    $('#privilege_desc').val('')
                }

                $('.act').iCheck({
                    // checkboxClass: 'icheckbox_square-blue',
                    // radioClass: 'iradio_square-blue',
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass   : 'iradio_flat-blue'
                })

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

                $('.menu').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass   : 'iradio_flat-blue'
                }).on('ifClicked', function (e) {
                    val = e.delegateTarget.value
                    if(e.currentTarget.checked){
                        $('.act_cloud_'+val).iCheck('uncheck')
                        $('.act_local_'+val).iCheck('uncheck')
                    }else{
                        $('.act_cloud_'+val).iCheck('check')
                        $('.act_local_'+val).iCheck('check')
                    }
                });

                $('#checkAll').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass   : 'iradio_flat-blue'
                }).on('ifClicked', function (e) {
                    if(e.currentTarget.checked){
                        $('.act').iCheck('uncheck')
                    }else{
                        $('.act').iCheck('check')
                    }
                });

                $('#actLocal').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass   : 'iradio_flat-blue'
                }).on('ifClicked', function (e) {
                    if(e.currentTarget.checked){
                        $('.actionsLocal').iCheck('uncheck')
                    }else{
                        $('.actionsLocal').iCheck('check')
                    }
                });          

                $('#actCloud').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass   : 'iradio_flat-blue'
                }).on('ifClicked', function (e) {
                    if(e.currentTarget.checked){
                        $('.actionsCloud').iCheck('uncheck')
                    }else{
                        $('.actionsCloud').iCheck('check')
                    }
                });                                
            }
        });
        
        $('.checkAll').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass   : 'iradio_flat-blue'
        })


    }
</script>
