
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
    
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open_multipart('master_data2/assetDevice/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,"",' class="form-control select2"   required '); ?>
                        </div>                        

                        <div class="col-sm-6 form-group">
                            <label>Tanggal Berlaku<span class="wajib">*</span></label>
                            <input type="text" name="start_date" id="startDate" class="form-control"  placeholder="Start Date" required>
                        </div>
                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-6 form-group">
                            <label>Tipe File<span class="wajib">*</span></label>
                            <select class="form-group select2 " required name="fileType" id="fileType">
                                <option value="" data-id="0" >Pilih</option>
                                <?php foreach ($fileType as $key => $value) { ?>
                                    <option value="<?= $this->enc->encode($key)?>" data-id="<?= $key ?>" ><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Module <span class="wajib">*</span></label>
                            <?= form_dropdown("module",$getModule,"",' class="form-control select2"  placeholder="Module" required '); ?>
                        </div>                        
                        <div class="col-sm-12  " >
                            <a class="pull-right btn  btn-warning" id="btn-tambah" title="Tambah File" style="display:none"><i class="fa fa-plus"></i></a>
                        </div>
                    </div>
                    <div class="row scrolling">
                        <div class="col-sm-12 form-group row group-gambar" id="div-upload-0" style="display:none">
                            <div class="col-sm-12 form-group" >                                       
                                <label class="input-group-text" for="inputGroupFile01">Upload File <span class="wajib">*</span></label>        
                                <div class="form-inline "  style="padding-bottom:10px;" >                                   
                                    <input type="file" class="form-control file-upload group-upload" id="fileUpload_0" name="fileUpload[0]"  required style="width:90%" onchange="myData.viewImg(0)"> 
                                    <a class=" btn  btn-danger pull-right btn-hapus"  title="Hapus File" onclick="hapus(0)">
                                        <i class="fa fa-trash-o"></i>
                                    </a>       
                                </div>                                                                                                                    
                            </div>
                            <!-- <div class="col-sm-6 form-group">
                                <label>Nama<span class="wajib">*</span></label>
                                <input type="text" name="name[0]" class="form-control group-upload"  placeholder="nama" required>
                            </div>                                                 -->
                            <div class="col-sm-6 form-group">
                                <label>Keterangan<span class="wajib">*</span></label>
                                <input type="text" name="desc[0]" class="form-control group-upload"  placeholder="Keterangan" required>
                            </div>       
                            <div class="col-sm-6 img-detail" id="img-detail-0"></div>                                                        
                            <div class="col-sm-12 "><h1><hr /></h1></div>              
    
                        </div>              
                        <div class="col-sm-12" id="footerFile"> </div>
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
        validateForm2('#ff',function(url,data){
            postData2(url,data);
        });

        $('#startDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            minuteStep:1,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });                

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width:"100%"
            });
        });

        let idx=1
        $(`#btn-tambah`).on("click", function(){
            $(myData.fileInput(idx)).insertBefore(`#footerFile`)
            $(`#div-upload-${idx}`).slideDown()
            idx ++

            let id = $(`#fileType option:selected`).data("id");
            if(id == 1)
            {
                $('.group-upload').each(function(i, obj) {
                    $(`#${obj.id}`).attr("accept", ".jpg, .jpeg, .png")
                });                
            }
            else
            {
                $('.group-upload').each(function(i, obj) {
                    $(`#${obj.id}`).attr("accept", ".mp4")
                });
            }            
        })
        
        $(`#fileType`).on("change", function(){
            $(`.group-upload`).val("");
            $(`.img-detail`).html("");
            let id = $(`#fileType option:selected`).data("id");
            if(id == 1)
            {
                $(`.btn-hapus`).show();
                $(`#btn-tambah`).slideDown();
                $(`.group-gambar`).slideDown();
                
                $('.group-upload').each(function(i, obj) {

                    $(`#${obj.id}`).attr("accept", ".jpg, .jpeg, .png")
                });                
            }
            else if(id==2)
            {
                $(`.btn-hapus`).hide();

                $(`#btn-tambah`).slideUp();
                $(`.group-gambar`).slideDown();

                $('.group-upload').each(function(i, obj) {
                    $(`#${obj.id}`).attr("accept", ".mp4")
                    // $(`#${obj.id}`).attr("accept", ".jpg, .jpeg, .png")
                });
            }
            else
            {
                $(`#btn-tambah`).slideUp();
                $(`.group-gambar`).slideUp();
            }

            $('.group-gambar').each(function(i, obj) {
                if(i > 0)
                {
                    $(`#${obj.id}`).remove()
                }
            });
       
        })
    })
     
</script>