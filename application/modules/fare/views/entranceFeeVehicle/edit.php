 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/entranceFeeVehicle/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,$portSelected,' class="form-control select2" id="port" required ') ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Golongan PNP<span class="wajib">*</span></label>
                            <?= form_dropdown("vehicleClass",$vehicleClass,$vehicleClassSelected,' class="form-control select2" id="vehicleClass" required ') ?>
                        </div>

                        <div class="col-sm-12"> </div>
                        <div class="col-sm-6 form-group">
                            <label>Tarif Entrance<span class="wajib">*</span></label>
                            <input type="text" name="entranceFee" class="form-control" value="<?= $detail->entrance_fee ?>" placeholder="Tarif Entrance" required onkeypress="return isNumberKey(event)">

                            <input type="hidden" name="id" required value="<?= $id ?>">
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