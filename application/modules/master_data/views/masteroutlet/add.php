<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/masteroutlet/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Merchant Name</label>
                            <?php echo form_dropdown("merchant_id", $mastermerchant, '', 'class="form-control select2" required>'); ?>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label>Outlet ID</label>
                            <input type="text" name="outlet_id" class="form-control" placeholder="ID Outlet" required>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Description" required>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Add'); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        validateForm('#ff', function(url, data) {
            postData(url, data);
        });
        $('.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>