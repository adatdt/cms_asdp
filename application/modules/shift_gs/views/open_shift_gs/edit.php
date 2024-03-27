 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 
<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_gs/open_shift_gs/action_edit', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-md-4 form-group">
                            <label>Kode Penugasan <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required disabled value="<?php echo $detail->shift_gs_code ?>">
                            <input type="hidden" required  value="<?php echo $this->enc->encode($detail->shift_gs_code) ?>" name='assignment'>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label>Tanggal Penugasan <span class="wajib">*</span></label>
                            <div class="input-group">
                                <input type="text" name="date"  class="form-control date" id="date" placeholder="YYYY-MM-DD" value="<?php echo $detail->shift_gs_date; ?>" required>
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="port" required id="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->port_id?"selected":""; ?>><?php echo $value->name ?></option>
                                <?php }?>                                
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Shift <span class="wajib">*</span></label>
                            <select class="form-control select2" name="shift" required id="shift" required="">
                                <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->shift_id?"selected":""; ?>><?php echo $value->shift_name ?></option>
                                <?php }?>                                
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Username <span class="wajib">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $detail->username ?>"  readonly="">
                        </div>    
                    </div>

                </div>
            </div>      
            <?php echo createBtnForm('Edit'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>

<script type="text/javascript">


    function action_remove(x)
    {
        $("#"+x).remove();   
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

      $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),

            });
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        });
    })

</script>
