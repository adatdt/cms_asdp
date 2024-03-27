 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/master_status/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <input type="hidden" name="id_data" value="<?php  echo $id ?>">

                        <div class="col-sm-6 form-group">
                            <label>Nama Tabel <span class="wajib">*</span></label>
                            <input type="text" name="tbl_name" class="form-control" placeholder="Nama Tabel" value="<?php echo $detail->tbl_name; ?>" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Status<span class="wajib">*</span></label>
                            <input type="text" name="status" class="form-control" placeholder="Kode Status" value="<?php echo $detail->status; ?>" onkeypress="return isNumberKey(event)" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Keterangan<span class="wajib">*</span></label>
                            <input type="text" name="description" class="form-control"  placeholder="Keterangan" value="<?php echo $detail->description; ?>" required >
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