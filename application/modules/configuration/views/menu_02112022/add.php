<style type="text/css">
    .textbox .textbox-text {
        font-size: 12px;
        border: 0;
        margin: 0;
        padding: 4px;
        white-space: normal;
        vertical-align: top;
        outline-style: none;
        resize: none;
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        border-radius: 0px;
        height: 33px !important;
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('configuration/menu/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nama Menu</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Menu" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Parent</label>
                            <input type="text" name="parent" id="parent" class="form-control" placeholder="Parent" style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Link</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?php echo base_url() ?>
                                </span>                        
                                <input type="text" name="link" class="form-control" placeholder="Link">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>Icon</label>
                            <?php echo form_dropdown('icon', $icon, '', 'class="form-control select2" data-placeholder="Pilih Icon Menu" id="icon" style="width: 100%;" required'); ?>
                        </div>
                        <div class="col-sm-3">
                            <label>Order</label>
                            <input type="text" name="ordering" class="form-control" placeholder="Order" onkeypress="return isNumberKey(event)" maxlength="3" required>
                        </div>
                    </div>
                </div>
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Aksi</label>
                            <?php echo form_dropdown('menuAction', $actions, '', 'class="form-control select2" id="menuAction" required data-placeholder="Pilih Aksi" multiple="multiple" style="width: 100%;"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.mfp-wrap').removeAttr('tabindex');
        $('#icon').select2({
            templateResult: iconFormat,
            templateSelection: iconFormat,
            escapeMarkup: function(m) { return m; }
        });
        $("#menuAction").select2();
        setTimeout(function(){
            $("#menuAction").val(['1','2','3','4']);
            $('#menuAction').trigger('change');
        },100)

        $('.select2').change(function(){
            $(this).valid()
        })

        $('#parent').combotreegrid({
            url         : '<?php echo site_url() ?>configuration/menu/get_list',
            method      : 'POST',
            striped     : false,
            fitColumns  : true,
            treeField   : 'name',
            idField     : 'id',
            height      : 'auto',
            selectOnNavigation: false,
            scrollbarSize: 0,
            nowrap      : true,
            sortable    : false,
            singleSelect: true,
            columns:[[
              { field: 'name', title: 'Menu Name', width: 100}
            ]],

            loadFilter:function(data){
                data.rows.push(
                    {id: 0, name: "No Parent"}
                );
                
                return data;
            },

            onLoadSuccess: function(row, data){
                $('.tree-folder').css('background','none');
                $('.tree-file').css('background','none');

                r = data.rows;
                for(i in r){
                    if(r[i].iconCls != ''){
                        $('.'+r[i].iconCls).html('<i class="'+r[i].iconCls+'"></i>');
                    }
                }
            }
        });

        validateForm('#ff',function(url,data){
            data.actions = $('#menuAction').val();
            postData(url,data,true);
        });
    })
</script>