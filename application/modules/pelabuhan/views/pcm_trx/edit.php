 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/pcm_trx/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?php echo form_dropdown("port",$port,$selectedPort, "class='form-control select2' id='port' required disabled") ?>

                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kelas Layanan<span class="wajib">*</span></label>
                            <?php echo form_dropdown("shipClass",$shipClass,$selectedShipClass, "class='form-control select2' id='shipClass' required disabled") ?>
                        </div>

                        <div class="col-sm-12"> </div>

                        <div class="col-sm-6 form-group">
                            <label>Golongan Kendaraan<span class="wajib">*</span></label>
                            <?php echo form_dropdown("vehicleClass",$vehicleClass,$selectedVehicleClass, "class='form-control select2' id='vehicleClass' required disabled") ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Quota yang tersedia<span class="wajib">*</span></label>
                            <input type="text" name="totalQuota" id="totalQuota" class="form-control"  placeholder="Quota yang tersedia" required disabled value="<?php echo $detail->total_quota ?>" >
                        </div>
                        

                        <div class="col-sm-12"> </div>

                        <div class="col-sm-6 form-group">
                            <label>Quota yang digunakan<span class="wajib">*</span></label>
                            <input type="text" name="usedQuota" class="form-control "  placeholder="Quota yang digunakan" required disabled value="<?php echo $detail->used_quota ?>">
                        </div>                        


                        <div class="col-sm-6 form-group">
                            <label>Keberangkatan<span class="wajib">*</span></label>
                            <input type="text" name="departDate" class="form-control date" id="departDate"  placeholder="YYYY-MM-DD" required readonly="" value="<?php echo $detail->depart_date ?>">
                        </div>

                        
                        <div class="col-sm-12"> </div>


                        <div class="col-sm-2 form-group">
                            <label>Jam<span class="wajib">*</span></label>
                            <?php echo form_dropdown("time",$time,$selectedTime, "class='form-control select2' id='time' required disabled") ?>
                        </div> 

                        <div class="col-sm-12"> <legend></legend></div>

                        <div class="col-sm-6 form-group">
                            <label>Quota yang ditambahkan<span class="wajib">*</span></label>
                            <input type="text" name="quota" class="form-control" id="quota"  placeholder="Quota" required onkeypress="return isNumberKey(event)" autocomplete="off">
                        </div>


                        <div class="col-sm-6 form-group">
                            <label>Aksi<span class="wajib">*</span></label>
                            <?php echo form_dropdown("action",$action,"", "class='form-control select2' id='action' required ") ?>
                        </div>

                        <div class="col-sm-12"> </div>


                        <div class="col-sm-6 form-group">
                            <label>Estimasi Quota<span class="wajib">*</span></label>
                            <input type="text" name="estimation" class="form-control " id="estimation"  placeholder="Estimasi" required readonly="" >
                        </div>                                                                        

                        <div class="col-sm-12"> </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    rules   = {quota: {number: true}}
    messages= {quota: {number: "Format Harus Angka"}}

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData('pcm_trx/action_edit',data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#action").change(()=>{
            myData.estimation()
        })

        $("#quota").keyup(()=>{
            myData.estimation()
        })

    })
</script>