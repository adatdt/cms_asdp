 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/quota_vehicle/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="port" required>
                                <option value="">Pilih</option>
                                <?php foreach ($port as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id)?>" <?php echo $value->id==$detail->port_id?"selected":""; ?> ><?php echo strtoupper($value->name)?> </option>
                                <?php }?>
                                
                            </select>
                            <input type="hidden" name="id" value="<?php echo $this->enc->encode($detail->id) ?>">
                            <input type="hidden" name="port" value="<?php echo $this->enc->encode($detail->port_id) ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" name="ship_class" required>
                                <option value="">Pilih</option>
                                <?php foreach ($ship_class as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id)?>" <?php echo $value->id==$detail->ship_class?"selected":""; ?> > <?php echo strtoupper($value->name)?> </option>
                                <?php }?>  
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Kendaraan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="vehicle_type" required>
                                <option value="">Pilih</option>
                                <?php foreach ($vehicle_type as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id)?>" <?php echo $value->id==$detail->vehicle_type?"selected":""; ?>><?php echo strtoupper($value->name)?> </option>
                                <?php }?>  
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Value <span class="wajib">*</span></label>
                            <input type="number" name="value" class="form-control" placeholder="Tipe Param" required onkeypress="return isNumberKey(event)" value="<?php echo $detail->param_value; ?>">
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