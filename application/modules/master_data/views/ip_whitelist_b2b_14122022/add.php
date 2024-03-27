
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/ip_whitelist_b2b/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>IP <span class="wajib">*</span></label>
                            <input type="text" name="ip" class="form-control" placeholder="IP" required maxlength="15" onkeypress="return isNumberKey(event)">
                        </div>

                        <!-- <div class="col-sm-6 form-group">
                            <label>ID Merchant<span class="wajib">*</span></label>
                            <input type="text" name="merchant" class="form-control"  placeholder="ID Merchant" required>
                        </div> -->
                        <div class="col-sm-6 form-group">
                            <label>Nama Merchant<span class="wajib">*</span></label>
                            <select id="merchant" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant">
                                <?php foreach($merchant as $key=>$value) {?>
                                    <option value="<?php echo $value->merchant_id; ?>"><?php echo strtoupper($value->merchant_name); ?></option>
                                <?php }?>
                            </select>    
                        </div>    

                        <!-- <div class="col-sm-12"> </div> -->
<!--                         <div class="col-sm-6 form-group">
                            <label>Biaya Transfer<span class="wajib">*</span></label>
                            <input type="text" name="transfer_fee" class="form-control"  placeholder="Biaya Transfer" required onkeypress="return isNumberKey(event)">
                        </div> -->

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
<script type="text/javascript">
    function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
</script>