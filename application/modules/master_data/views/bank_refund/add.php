
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/bank_refund/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Bank ABBR <span class="wajib">*</span></label>
                            <input type="text" name="abbr" class="form-control"  placeholder="ABBR" required maxlength="10">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Bank<span class="wajib">*</span></label>
                            <input type="text" name="bank" class="form-control"  placeholder="Nama Bank" required>
                        </div>

                        <div class="col-sm-12"> </div>
                        <div class="col-sm-6 form-group">
                            <label>Biaya Transfer<span class="wajib">*</span></label>
                            <input type="text" name="transfer_fee" class="form-control"  placeholder="Biaya Transfer" required onkeypress="return isNumberKey(event)">
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

        rules = { abbr :{maxlength: 10}}
        messages= {abbr :{maxlength: jQuery.validator.format("Max {0} Karakter")}};

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