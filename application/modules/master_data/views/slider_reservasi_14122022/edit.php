<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/slider_reservasi/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <!-- <div class="col-sm-6 form-group">
                            <label>Kode Laporan<span class="wajib">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder='Kode Laporan' required readonly value="<?php echo $detail->report_code ?>">
                        </div> -->

                        <div class="col-sm-12 form-group">
                            <label>Order<span class="wajib">*</span></label>
                            <input type="text" name="order" class="form-control" placeholder='Order' required value="<?php echo $detail->order ?>">
                        </div>
                        <div class="col-sm-12 form-group">
                            <label>Description<span class="wajib">*</span></label>
                            <input type="text" name="desc" class="form-control" placeholder='Description' required value="<?php echo $detail->desc ?>">
                        </div>
                        <div class="col-sm-12 form-group">
                            <label>URL</label>
                            <input type="text" name="url" class="form-control" placeholder='URL' value="<?php echo $detail->url_target ?>">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
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