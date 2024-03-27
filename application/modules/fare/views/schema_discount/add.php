 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{
        color:red;
    }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/schema_discount/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Schema <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" required placeholder="Nama Schema">

                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Slug <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="slug" id="slug" required placeholder="Slug">

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

    })
</script>