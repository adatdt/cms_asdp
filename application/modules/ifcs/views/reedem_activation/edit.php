
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/reedem_activation/action_edit', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan<span class="wajib">*</span></label>
                            <input type="hidden" required value="<?php echo $id?>" name="id">
                            <?php echo form_dropdown("port",$port,$port_selected,' id="port" class="form-control js-data-example-ajax select2" required ') ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Golongan<span class="wajib">*</span></label>
                            <?php echo form_dropdown("vehicle_class",$vehicle_class,$vehicle_class_selected,' id="vehicle_class" class="form-control js-data-example-ajax select2 " required ') ?>
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Kapal<span class="wajib">*</span></label>
                            <?php echo form_dropdown("ship_class",$ship_class,$ship_class_selected,' id="ship_class" class="form-control js-data-example-ajax select2 " required ') ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Redeem<span class="wajib">*</span></label>
                            <?php echo form_dropdown("reedem",$reedem,$reedem_selected,' id="reedem" class="form-control js-data-example-ajax select2 " required ') ?>
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