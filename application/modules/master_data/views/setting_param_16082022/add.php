
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/setting_param/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Param<span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Param" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Value Param <span class="wajib">*</span></label>
                            <input type="text" name="value_param" class="form-control" placeholder="Value Param" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Param <span class="wajib">*</span></label>
                            <input type="text" name="tipe_param" class="form-control" placeholder="Tipe Param" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Value <span class="wajib">*</span></label>
                            <input type="text" name="tipe_value" class="form-control" placeholder="Tipe Value" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Info <span class="wajib">*</span></label>
                            <input type="text" name="info" class="form-control" placeholder="Info" required>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });
    })
</script>