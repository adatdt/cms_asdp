<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
        <form action="<?php echo site_url() ?>pelabuhan/pcm_trx_global/action_edit_import_excel" id="ff" role="form" method="POST" enctype="multipart/form-data">
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <div class="form-group">

                                <div class="col-sm-6 form-group">
                                    <label>Pelabuhan <span class="wajib">*</span></label>
                                    <?php echo form_dropdown("port",$port,"", "class='form-control select2' id='port' required") ?>
                                </div>        
                                <div class="col-sm-6 form-group">
                                    <label>Aksi<span class="wajib"> *</span></label>
                                    <?php echo form_dropdown("action",$action,"", "class='form-control select2' id='action' required ") ?>
                                </div>

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
                                                <input type="hidden"><input type="file" name="excel"> </span>
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
            myData.postData2('pcm_trx_global/action_edit_import_excel',data);         
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

    })
</script>