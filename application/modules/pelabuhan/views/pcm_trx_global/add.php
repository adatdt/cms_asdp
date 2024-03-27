
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/pcm_trx_global/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?php echo form_dropdown("port",$port,"", "class='form-control select2' id='port' required") ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kelas Layanan<span class="wajib">*</span></label>
                            <?php echo form_dropdown("shipClass",$shipClass,"", "class='form-control select2' id='shipClass' required ") ?>
                        </div>

                        <div class="col-sm-12"> </div>

                        <div class="col-sm-6 form-group">
                            <label>Golongan Kendaraan<span class="wajib">*</span></label>
                            <?php echo form_dropdown("vehicleClass",$vehicleClass,"", "class='form-control select2' id='vehicleClass' required ") ?>
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Quota<span class="wajib">*</span></label>
                            <input type="text" name="quota" class="form-control" id="quota"  placeholder="Quota" required onkeypress="return isNumberKey(event)">
                        </div>
                        
                        <div class="col-sm-12"> </div>

                        <div class="col-sm-6 form-group">
                            <label>Keberangkatan<span class="wajib">*</span></label>
                            <input type="text" name="departDate" class="form-control date" id="departDate"  placeholder="YYYY-MM-DD" required readonly="">
                        </div>

                        <div class="col-sm-2 form-group">
                            <label>Jam<span class="wajib">*</span></label>
                            <?php echo form_dropdown("time",$time,"", "class='form-control select2' id='time' required ") ?>
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

    rules   = {quota: {number: true}}
    messages= {quota: {number: "Format Harus Angka"}}

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
        });        
    })
</script>