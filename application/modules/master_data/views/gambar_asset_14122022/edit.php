<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/gambar_asset/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <!-- <div class="col-sm-6 form-group">
                            <label>Kode Laporan<span class="wajib">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder='Kode Laporan' required readonly value="<?php echo $detail->report_code ?>">
                        </div> -->

                        
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
							</div>
                        
                            <div class="col-sm-12 form-group">
                            <label>Module<span class="wajib">*</span></label>
                            <input type="text" name="module" class="form-control" placeholder='Module' required value="<?php echo $detail->module ?>">
                            </div>
                            <div class="col-sm-12 form-group">
                            <label>Description<span class="wajib">*</span></label>
                            <input type="text" name="desc" class="form-control" placeholder='Description' required value="<?php echo $detail->desc ?>">
                            </div>
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <input type="hidden" name="order" value="<?php echo $detail->order ?>">
                            <input type="hidden" name="name" value="<?php echo $detail->name ?>">
                            <input type="hidden" name="path" value="<?php echo $detail->path ?>">
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    // $(document).ready(function(){
    //     validateForm('#ff',function(url,data){
    //         postData(url,data);
    //     });

    //     $('.select2:not(.normal)').each(function () {
    //         $(this).select2({
    //             dropdownParent: $(this).parent()
    //         });
    //     });
    // })
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
					  console.log(json)
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
                // document.getElementById('tempatfile').innerHTML = "";
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