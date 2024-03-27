 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/user_ifcs_type/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Nama Tipe User IFCS<span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Tipe User IFCS" required value="<?php echo $detail->name ?>">
                            <input type="hidden" name="id" value="<?php echo $this->enc->encode($detail->id); ?>" >
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