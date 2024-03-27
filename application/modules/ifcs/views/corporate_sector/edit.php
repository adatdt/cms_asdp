 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/corporate_sector/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Kode Corporate Sector<span class="wajib">*</span></label>
                            <input type="text" name="code" class="form-control"  placeholder="Kode Corporate Sector" value="<?php echo $detail->business_sector_code?>" required readonly>
                        </div>
                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-12 form-group">
                            <label>Nama Corporate Sector<span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama Corporate Sector" value="<?php echo $detail->description?>" required>
                            <input type="hidden" name="corporate_sector" value="<?php echo $id; ?>">
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

        rules   = {email: "required email"};
        messages= {email: "Format email tidak valid"};

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