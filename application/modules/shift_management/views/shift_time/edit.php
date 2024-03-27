<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/shift_time/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
               <div class="form-group">


                <div class="row">
                    
                    <div class="col-sm-6 form-group">
                        <label>Pelabuhan <span class="wajib">*</span></label>
                        <select id="port" class="form-control js-data-example-ajax select2" dir="" name="port" required>
                            <option value="">Pilih</option>
                            <?php  foreach($port as $key=>$value ) { ?>
                                <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->port_id?"selected":""; ?>><?php echo $value->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>                        

                    <div class="col-sm-6 form-group">
                        <label>Shift <span class="wajib">*</span></label>
                        <select id="shift" class="form-control js-data-example-ajax select2" dir="" name="shift" required >
                            <option value="">Pilih</option>
                            <?php  foreach($shift as $key=>$value ) { ?>
                                <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->shift_id?"selected":""; ?>><?php echo $value->shift_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>                        

                    <div class="col-sm-12 form-group"></div>

                    <div class="col-sm-6 form-group">
                        <label>Jam Awal Shift <span class="wajib">*</span></label>
                        <input type="time" class="form-control" required name="login_shift" id="login_shift" placeholder="00:00" value="<?php echo $detail->shift_login ?>">
                        <input type="hidden" class="form-control" required name="shift_time" id="shift_time" value="<?php echo $id ?>">

                    </div>

                    <div class="col-sm-6 form-group">
                        <label>Jam Akhir Shift <span class="wajib">*</span></label>
                        <input type="time" class="form-control" required name="logout_shift" id="logout_shift" placeholder="00:00" value="<?php echo $detail->shift_logout ?>">
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