
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/reward_sector/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Tier <span class="wajib">*</span></label>
                            <input type="text" name="tier" class="form-control"  placeholder="Nama Tier" required >
                        </div> 

                        <div class="col-sm-6 form-group">
                            <label>Nama Sector <span class="wajib">*</span></label>
                            <?php echo form_dropdown('sector',$sector,'',' class="form-control select2"  required '); ?>
                        </div> 

                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>MIN Tier <span class="wajib">*</span></label>
                            <input type="text" name="min" class="form-control"  placeholder="Min Tier" onkeypress="return isNumberKey(event)" required >
                        </div> 

                        <div class="col-sm-6 form-group">
                            <label>Max Tier <span class="wajib">*</span></label>
                            <input type="text" name="max" class="form-control"  placeholder="Max Tier" onkeypress="return isNumberKey(event)" required >
                        </div>           

                        <div class="col-sm-12 "></div>                        

                        <div class="col-sm-6 form-group">
                            <label>Persen <span class="wajib">*</span></label>
                            <input type="number" name="persen" class="form-control"  placeholder="Persen"  required >
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

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });        

    })
</script>