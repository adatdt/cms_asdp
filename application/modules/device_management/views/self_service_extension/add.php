<style type="text/css">
    .wajib{
        color: red;
    }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/self_service_extension/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select id="port" class="form-control js-data-example-ajax select2" dir="" name="port" required>
                                <option value="">Pilih</option>
                                <?php  foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>                        

                        <div class="col-sm-6 form-group">
                            <label>Username phone<span class="wajib">*</span></label>
                            <input type="text" name="usernamePhone" class="form-control" placeholder="Username phone" required>
                        </div>                        

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Extension phone <span class="wajib">*</span></label>
                            <input type="text" name="extensionPhone" class="form-control" placeholder="Extension Phone" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Password phone<span class="wajib">*</span></label>
                            <input type="text" name="passwordPhone" class="form-control" placeholder="Password Phone" required>
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
        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),

            });
        });

    })
</script>