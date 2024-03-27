 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pos_action/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Nama Aksi POS <span class="wajib">*</span></label>
                            <input type="text" name="action_name" class="form-control"  placeholder="Aksi POS" value="<?php echo $detail->action_code; ?>" disabled required>
                        </div>

                        <div class="col-sm-12 form-group">
                            <label>Keterangan<span class="wajib">*</span></label>
                            <textarea name="description" placeholder="Keterangan" class="form-control" required><?php echo $detail->description; ?></textarea>
                            <input type="hidden" name="id" value="<?php echo $id ?>" required>
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