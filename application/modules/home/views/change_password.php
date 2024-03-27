<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('home/action_change_password', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Masukan Password</label>
                            <input type="password" class="form-control"  name="pass" id="pass"  placeholder="password"  required>
                            <input type="hidden"  name="id" class="id" autocomplete="off" value="<?php echo $this->enc->encode($detail->id) ?>">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Password Baru</label>
                            <input type="password" class="form-control"  name="newpass" id="newpass" placeholder="Password Baru"  autocomplete="off"  required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Ulangi Password</label>
                            <input type="password" class="form-control"  name="repass" id="repass"  placeholder="Ulangi Password" required>
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
            sendData(url,data);
        });


    // function ambil_data(){
    //     $.ajax({
    //         type:"POST",
    //         url:"<?php echo site_url()?>home/ambil_data",
    //         dataType:"json",
    //         success:function(x)
    //     });
    // }

        function sendData(url,data){
            $.ajax({
                url         : url,
                data        : data,
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1){
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        // $('#dataTables').DataTable().ajax.reload();
                        ambil_data();
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
    });
</script>