
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pos_action/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Nama Aksi POS <span class="wajib">*</span></label>
                            <input type="text" name="action_name" class="form-control"  placeholder="Aksi POS" id="action_name" required>
                        </div>

                        <div class="col-sm-12 form-group">
                            <label>Keterangan<span class="wajib">*</span></label>
                            <!-- <input type="text" name="description" class="form-control"  placeholder="Keterangan" required> -->
                            <textarea name="description" placeholder="Keterangan" class="form-control" required></textarea>
                        </div>

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

        $("[name='action_name']").keyup(function(){
            var x = $(this).val();
            $(this).val(x.toUpperCase());
        })

    })
</script>