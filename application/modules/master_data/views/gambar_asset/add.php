<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib {
        color: red
    }

    .form-group .form-text {
        font-size: 1.2rem;
        font-weight: 400;
        font-style: italic;
    }
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/gambar_asset/action_import_excel', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <!-- <div class="col-md-12" > -->
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="col-sm-12 form-group">
                            <label>Pilih File Gambar</label>
                            <div class="input-group ">

                                <div class="form-control uneditable-input   input-fix" id="tempatfile" data-trigger="fileinput">
                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                    <span class="fileinput-filename"> </span>
                                </div>
                                <span class="input-group-addon btn default btn-file">
                                    <span class="fileinput-new"> Pilih File </span>
                                    <span class="fileinput-exists"> Pilih File</span>
                                    <input type="hidden"><input type="file" name="berkas"> </span>
                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Hapus </a>
                            </div>
                            <span class="form-text text-muted">(*.jpg, *.jpeg, *.png)</span>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label>Module<span class="wajib">*</span></label>
                            <input type="text" name="module" class="form-control" placeholder='Module' required>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label>Description<span class="wajib">*</span></label>
                            <input type="text" name="desc" class="form-control" placeholder='Description' required>
                        </div>

                        <div class="col-sm-12 form-group">
                            <label>URL</label>
                            <input type="text" name="url" class="form-control" placeholder='URL'>
                        </div>
                        <!-- <label>Nama<span class="wajib">*</span></label>
                                            <input type="text" name="name" class="form-control" placeholder='Nama' required> -->
                    </div>
                    <!-- </div> -->
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        validateForm('#ff', function(url, data) {
            $.ajax({
                url: url,
                data: new FormData($('form')[0]),
                type: 'POST',
                dataType: 'json',
                contentType: false,
                processData: false,


                beforeSend: function() {
                    unBlockUiId('box')
                },


                success: function(json) {
                    //   console.log(json)
                    if (json.code == 1) {
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        // if(y){
                        //     $('#grid').treegrid('reload');
                        //     $('#dataTables').DataTable().ajax.reload();
                        //     // ambil_data();
                        // }
                        // else{
                        //     $('#dataTables').DataTable().ajax.reload();
                        //     // ambil_data();

                        // }
                        $('#dataTables').DataTable().ajax.reload();
                        // document.getElementById('tempatfile').innerHTML = "";
                    } else {
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function() {
                    $('#box').unblock();
                }
            });
        });

    })
</script>