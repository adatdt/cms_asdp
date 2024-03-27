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
                    <form id="ff" action="<?php echo site_url('configuration/privilege/action_privilege'); ?>" method="post">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Nama Group</label>
                                <?php echo form_dropdown('group_id', $usergroup, '', 'class="form-control select2" id="group" required data-placeholder="Pilih User Grup"'); ?>
                            </div>
                            <!-- <div class="col-md-4" id="access" style="display: none">
                                <label>Nama Hak Akses</label>
                                <input type="text" name="privilege_name" class="form-control" id="privilege_name" required>
                            </div>
                            <div class="col-md-5" id="desc" style="display: none">
                                <label>Deskripsi</label>
                                <textarea name="privilege_desc" class="form-control" id="privilege_desc" rows="2" style="resize: none"></textarea>
                            </div>-->
                        </div> 
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <label><input type="checkbox" id="checkAll" disabled style="display: none"/><span id="check">Check All</span></label>
                            </div> 
                        </div>    
                        <table class="table table-bordered table-hover" id="grid"></table>
                        <br>
                        <button type="submit" class="btn btn-warning pull-right" id="save" style="display: none">Simpan</button>
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

        $('#group').on('select2:selecting', function(e) {
            val = e.params.args.data.id;
            gridPrivilege(val);
            $('#checkAll').iCheck('enable');
            $('#checkAll').iCheck('uncheck');
        });

        // $('#ff').validate({
        //     ignore      : 'input[type=hidden], .select2-search__field', 
        //     errorClass  : 'validation-error-label',
        //     successClass: 'validation-valid-label',
        //     rules       : rules,
        //     messages    : messages,

        //     highlight   : function(element, errorClass) {
        //         $(element).addClass('val-error');
        //     },

        //     unhighlight : function(element, errorClass) {
        //         $(element).removeClass('val-error');
        //     },

        //     errorPlacement: function(error, element) {
        //         if (element.parents('div').hasClass('has-feedback')) {
        //             error.appendTo( element.parent() );
        //         }

        //         else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
        //             error.appendTo( element.parent() );
        //         }

        //         else {
        //             error.insertAfter(element);
        //         }
        //     },

        //     submitHandler: function(form) {
        //         data = getFormData($(form));
        //         $.ajax({
        //             url      : form.action,
        //             data     : $(form).serialize(),
        //             type     : 'POST',
        //             dataType : 'json',

        //             beforeSend: function(){
        //                 $.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});
        //             },

        //             success: function(json) {
        //                 if(json.code == 1){
        //                     toastr.success(json.message, 'Sukses');
        //                     $('#grid').treegrid('reload');
        //                     App.alert('close')
        //                 }else if(json.code == 2){
        //                     App.alert({ 
        //                         container: '#alerts',
        //                         message: json.message,
        //                         type: 'danger',
        //                         close: true,
        //                         icon: 'fa fa-ban'
        //                     });
        //                 }else{
        //                     toastr.error(json.message, 'Gagal');
        //                 }
        //             },

        //             error: function() {
        //                 toastr.error('Silahkan Hubungi Administrator', 'Gagal');
        //             },

        //             complete: function(){
        //                 $.unblockUI();
        //             }
        //         });
        //     }
        // });

        validateForm('#ff',function(url,data){
            // data.actions = $('#menuAction').val();
            // postData(url,data,true);
            act = $(".actions")
            arr = [];
            for (var i = 0; i < act.length; ++i) {
                action = $(act[i])[0];
                if(action.checked){
                    p = action.dataset;
                    arr[i] = {
                        menu_id: p.menu_id,
                        detail_id: p.detail_id,
                        privilege_id: p.privilege,
                        status: p.status,
                    };
                }
                // console.log(action.checked)
            }
            data.actions = arr;
            postData(url,data,true);

            // console.log($(act))
        });
    });

    function gridPrivilege(group_id){
        $.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});

        $('#grid').treegrid({
            url         : '<?php echo site_url() ?>configuration/privilege/get_list',
            queryParams : {
                group : group_id
            },
            method      : 'POST',
            striped     : false,
            fitColumns  : true,
            treeField   : 'name',
            idField     : 'id',
            emptyMsg    : 'Tidak Ada Data.',
            loadMsg     : '',
            scrollbarSize: 0,
            nowrap      : false,
            sortable    : false,
            singleSelect: true,
            columns:[[
                { field: 'menu_id', title: 'CHECK ALL', width: 15,align: 'center'},
                { field: 'name', title: 'NAMA MENU', width: 50},
                { field: 'action', title: 'AKSI', width: 100},
                // { field: 'view', title: 'VIEW', width: 20,align: 'center'},
                // { field: 'add', title: 'ADD', width: 20,align: 'center'},
                // { field: 'edit', title: 'EDIT', width: 20,align: 'center'},
                // { field: 'delete', title: 'DELETE', width: 20,align: 'center'},
                // { field: 'detail', title: 'DETAIL', width: 20,align: 'center'},
                // { field: 'approval', title: 'APPROVAL', width: 20,align: 'center'},
            ]],

            onLoadSuccess: function(row, data){
                $.unblockUI();
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
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
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
                        $('.act_'+val).iCheck('uncheck')
                    }else{
                        $('.act_'+val).iCheck('check')
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
            }
        });
    }
</script>
