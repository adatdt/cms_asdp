<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/mobileVersion/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Tipe <span class="wajib">*</span></label>
                            <input type="text" name="type" class="form-control" placeholder="Nama Tipe " required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Versi <span class="wajib">*</span></label>
                            <input type="text" name="version" class="form-control" placeholder="Versi" required>
                        </div>

                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Keterangan<span class="wajib">*</span></label>
                            <input type="text" name="description" class="form-control" placeholder="Keterangan" required>
                        </div>                        

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