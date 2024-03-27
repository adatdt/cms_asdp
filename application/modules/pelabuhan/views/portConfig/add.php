<style type="text/css">
    .wajib{ color:red; }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/portConfig/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,"",'class="form-control select2" required') ?>

                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" required >
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Config Group<span class="wajib">*</span></label>
                            <input type="text" name="configGroup" class="form-control" placeholder="Config Group" required >
                        </div> 

                        <div class="col-sm-6 form-group">
                            <label>Value <span class="wajib">*</span></label>
                            <input type="text" name="valueData" class="form-control" placeholder="Value" required >
                        </div>
                        <input type="hidden" id='tokenHash' class="tokenHash"  value="<?php echo $this->security->get_csrf_hash(); ?>" name="<?php echo $this->security->get_csrf_token_name(); ?>" >
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
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
