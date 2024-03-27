<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/masteroutlet/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
    
                        <div class="col-sm-12 form-group">
                            <label>Merchant Name</label>
                                <select class="form-control select2" disabled> 
                                <option value="">Pilih</option>
                                <?php foreach ($mastermerchant as $key => $value) { ?>
                                    <option <?php if ($value->merchant_id==$detail->merchant_id) {
                                        echo 'selected';
                                    } else { echo ''; } ?> value="<?php echo $this->enc->encode($value->merchant_id) ?>" <?php echo $detail->merchant_id==$value->id?"selected":""; ?> > <?php echo strtoupper($value->merchant_name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label >Outlet Id</label>
                            <input style="background-color:#EEF1F5;" type="text" name="outlet_id" class="form-control" placeholder="Nama Outlet" disabled required value="<?php echo $detail->outlet_id; ?>">
                        </div>
                        <div class="col-sm-12 form-group">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Description" required value="<?php echo $detail->description; ?>">
                         </div>
                         <div class="col-sm-12 form-group">
                            <input type="hidden" name="id" value="<?php echo $this->enc->encode($detail->id) ?>">
                        </div>

                        

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
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