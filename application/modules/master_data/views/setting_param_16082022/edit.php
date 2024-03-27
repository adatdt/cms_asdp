 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/setting_param/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Param" required value="<?php echo $detail->param_name; ?>" disabled>
                            <input type="hidden" name="id" value="<?php echo $this->enc->encode($detail->param_id) ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Value Param <span class="wajib">*</span></label>
                            <input type="text" name="value_param" class="form-control" placeholder="Value Param" required value="<?php echo $detail->param_value; ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Param <span class="wajib">*</span></label>
                            <input type="text" name="tipe_param" class="form-control" placeholder="Tipe Param" required value="<?php echo $detail->type; ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Value <span class="wajib">*</span></label>
                            <?php 
                                // custom saat param verification_time , maka tipe value tidak bisa di edit
                                $customTipeValue=$detail->param_name=="verification_time"?"readonly":"";
                             ?>                            
                            <input type="text" name="tipe_value" class="form-control" placeholder="Tipe Value" <?= $customTipeValue ?> required value="<?php echo $detail->value_type; ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Info <span class="wajib">*</span></label>
                            <input type="text" name="info" class="form-control" placeholder="Info"  required value="<?php echo $detail->info; ?>">
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>