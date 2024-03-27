 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/bank_refund/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Abbr <span class="wajib">*</span></label>
                            <input type="text" name="abbr" class="form-control"  placeholder="ABBR"
                            value="<?php echo $detail->bank_abbr ?>" required disabled  maxlength="10">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Bank <span class="wajib">*</span></label>
                            <input type="text" name="bank" class="form-control"  placeholder="Nama Bank"
                            value="<?php echo $detail->bank_name ?>" required>
                            <input type="hidden" name="id"  value="<?php echo $id ?>">
                        </div>

                        <div class="col-sm-12"> </div>
                        <div class="col-sm-6 form-group">
                            <label>Biaya Transfer<span class="wajib">*</span></label>
                            <input type="text" name="transfer_fee" class="form-control"  placeholder="Biaya Transfer" required onkeypress="return isNumberKey(event)" value="<?php echo $detail->transfer_fee ?>">
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