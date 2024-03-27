
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pids/labelPids/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Pelabuhan <span class="wajib">*</span></label>
                                <?= form_dropdown("port",$port,"",' class="form-control select2"  placeholder="Pelabuhan" required ')?>
                            </div>
                  
                            <div class="form-group">
                                <label>Nama Label (ID)<span class="wajib">*</span></label>
                                <input type="text" name="label_name" class="form-control"  placeholder="Nama Label" required>
                            </div>   

                        </div>

                        <div class="col-sm-6 form-group">

                            <div class="form-group">
                                <label>Nama <span class="wajib">*</span></label>
                                <input type="text" name="name" class="form-control"  placeholder="name" required>
                            </div>

                            <div class="form-group">
                                <label>Nama Label (EN)<span class="wajib">*</span></label>
                                <input type="text" name="label_name_en" class="form-control"  placeholder="Nama Label" required>
                            </div>                                                                                              

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
            postData2(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        function postData2(url,data,y){
            form = $('form')[0];
            formData = new FormData(form);

            $.ajax({
                url         : url,
                data        :formData,
                type        : 'POST',
                // enctype: 'multipart/form-data',
                processData: false,  // Important!
                contentType: false,
                cache:false,
                dataType    : 'json',

                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1)
                    {
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');

                        $('#dataTables').DataTable().ajax.reload( null, false );
                        socket.emit('pidsUpdateParams', parseInt(json.data['portId']));

                    }
                    else
                    {
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){
                    $('#box').unblock(); 
                }
            });
        }                
    })
</script>