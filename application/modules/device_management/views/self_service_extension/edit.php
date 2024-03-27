<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/self_service_extension/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select22" required name="port" id="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->port_id?"selected":""; ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Username phone<span class="wajib">*</span></label>
                            <input type="text" name="usernamePhone" class="form-control" placeholder="Username phone" required value="<?php echo $detail->username_phone ?>">
                        </div>                        

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Extension phone <span class="wajib">*</span></label>
                            <input type="text" name="extensionPhone" class="form-control" placeholder="Extension Phone" required value="<?php echo $detail->extension_phone ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Password phone<span class="wajib">*</span></label>
                            <input type="text" name="passwordPhone" class="form-control" placeholder="Password Phone" required value="<?php echo $detail->password_phone ?>">
                        </div>
                        <input type="hidden" name="id" required value="<?php echo $this->enc->encode($detail->id) ?>">          

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">

$(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select22:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        // $("#port").on("change",function(){
        //     get_dock();
        // });

        // $('.date').datepicker({
        //     format: 'yyyy-mm-dd',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     // endDate: new Date(),
        // });

        // $('.waktu').datetimepicker({
        //     format: 'yyyy-mm-dd hh:ii',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     // endDate: new Date(),
        // });

    })    
     
</script>