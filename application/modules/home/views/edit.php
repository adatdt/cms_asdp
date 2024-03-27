<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('home/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Username</label>
                            <input type="text" class="form-control"  name="username" id="username" value="<?php echo $detail->username?>" placeholder="Username" disabled>
                            <input type="hidden"  name="id" class="id" autocomplete="off" value="<?php echo $this->enc->encode($detail->id); ?>">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Group</label>
                            <input type="text" class="form-control"  name="group" id="group" value="<?php echo $detail->group_name?>" placeholder="Username" disabled>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama Depan</label>
                            <input type="text" class="form-control"  name="first_name" id="first_name" placeholder="First Name"  autocomplete="off" value="<?php echo $detail->first_name?>" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama Belakang</label>
                            <input type="Text" class="form-control"  name="last_name" id="last_name" placeholder="Last Name"  autocomplete="off" value="<?php echo $detail->last_name?>">
                        </div>


                        <div class="col-sm-4 form-group">
                            <label>No Telpon</label>
                            <input type="text" class="form-control"  name="phone" id="phone" placeholder="Telpon" value="<?php echo $detail->phone?>" onkeypress="return isNumberKey(event)" minlength="10" required>
                            
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <input type="text" class="form-control"  name="port" id="port" placeholder="Pelabuhan" value="<?php echo $detail->port_name?>" disabled>

                        </div>

                    </div>
                </div>
            </div>
            <!-- <?php echo createBtnForm('Update') ?> -->

                <div class="box-footer text-right">
                <button type="button" class="btn btn-sm btn-default" onclick="closeModal()"><i class="fa fa-close"></i> Batal</button>
                <button type="submit" class="btn btn-sm btn-primary" id="saveBtn"><i class="fa fa-check"></i> Edit</button>
                </div>


            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            // postData(url,data);
            sendData(url,data);
        });

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
    })
</script>