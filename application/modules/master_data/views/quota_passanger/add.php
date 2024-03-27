
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/quota_passanger/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="port" required>
                                <option value="">Pilih</option>
                                <?php foreach ($port as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id)?>"><?php echo strtoupper($value->name)?> </option>
                                <?php }?>
                                
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" name="ship_class" required>
                                <option value="">Pilih</option>
                                <?php foreach ($ship_class as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id)?>"><?php echo strtoupper($value->name)?> </option>
                                <?php }?>  
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Value <span class="wajib">*</span></label>
                            <input type="number" name="value" class="form-control" placeholder="Tipe Param" required onkeypress="return isNumberKey(event)">
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

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>