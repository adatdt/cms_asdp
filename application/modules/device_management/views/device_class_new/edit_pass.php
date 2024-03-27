<style type="text/css"> .wajib{color: red} </style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/device_class/action_edit_password', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">

                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Kode Perangkat <span class="wajib">*</span></label>
                            <input type="text" name="device_code" class="form-control" placeholder="Kode Perangkat" value="<?= $detail->terminal_code ?>" required  readonly >
                            <input type="hidden" name="device_terminal_id" value="<?= $this->enc->encode($detail->device_terminal_id); ?>">

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama Perangkat <span class="wajib">*</span></label>
                            <input type="text" name="device_name" class="form-control" placeholder="Nama Perangkat" required value="<?= $detail->terminal_name ?>" readonly>

                        </div>                        

                        <div class="col-sm-4 form-group">
                            <label>Password Lama <span class="wajib">*</span></label>
                            <input type="password" name="old_pass" class="form-control" placeholder="Password Lama" required >

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Password Baru <span class="wajib">*</span></label>
                            <input type="password" name="new_pass" class="form-control" placeholder="Password Baru" required >

                        </div>                                                
                        <div class="col-sm-4 form-group">
                            <label>Ulangi Password Baru<span class="wajib">*</span></label>
                            <input type="password" name="repeat_pass" class="form-control" placeholder="Ulangi Password Baru" required >

                        </div>                        

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
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
