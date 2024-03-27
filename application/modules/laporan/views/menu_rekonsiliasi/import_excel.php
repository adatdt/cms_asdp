<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
        <form action="<?php echo site_url() ?>laporan/menu_rekonsiliasi/action_import_excel" id="ff" role="form" method="POST" enctype="multipart/form-data">
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <div class="form-group">
                                <div class="col-md-12" >
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <label>Pilih File xlsx</label>
                                        <div class="input-group ">

                                            <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                <span class="fileinput-filename"> </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput-new"> Pilih File </span>
                                                <span class="fileinput-exists"> Pilih File</span>
                                                <input type="hidden"><input type="file" name="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required> </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Hapus </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?> 
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">


    $(document).ready(function(){
        validateForm('#ff',function(url,data){
        $.ajax({
        url         : url,
        data        : new FormData($('form')[0]),
        type        : 'POST',
        dataType    : 'json',
        contentType :false,
        processData :false,


        beforeSend: function(){
            unBlockUiId('box')
        },

        success: function(json) {
            // console.log(json)
            if(json.code == 1){
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
                totalrow();
            }else{
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
        });

    })
</script>