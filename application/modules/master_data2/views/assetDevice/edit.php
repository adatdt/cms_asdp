 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data2/assetDevice/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label> Kode Grup Perangkat<span class="wajib">*</span></label>
                            <input type="text" name="groupCode" class="form-control"  placeholder="Kode grup Perangkat"  disabled value="<?= $detail[0]->group_code_assets; ?>" >
                            <input type="hidden" required name="code" value="<?= $this->enc->encode($detail[0]->group_code_assets); ?>" >
                        </div>         
                        
                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,$portSelected,' class="form-control select2"   required '); ?>
                        </div>                            

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Tanggal Berlaku<span class="wajib">*</span></label>
                            <input type="text" name="start_date2" id="startDate" class="form-control"  placeholder="Start Date" required value="<?= date("Y-m-d H:i", strtotime($detail[0]->start_date)); ?>" disabled>
                            <input type="hidden" name="start_date"  required value="<?= $this->enc->encode(date("Y-m-d H:i", strtotime($detail[0]->start_date))); ?>" >
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Tipe File<span class="wajib">*</span></label>
                            <select class="form-group select2 " required name="fileType" id="fileType">
                                <option value="" data-id="0" >Pilih</option>
                                <?php foreach ($fileType as $key => $value) { ?>
                                    <option value="<?= $this->enc->encode($key)?>" data-id="<?= $key ?>" <?= $key==$detail[0]->file_type?"selected":""; ?>><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-12  " ></div>
                        <div class="col-sm-6 form-group">
                            <label>Module <span class="wajib">*</span></label>
                            <?= form_dropdown("module",$getModule,$getModuleSelected,' class="form-control select2"  placeholder="Module" required '); ?>

                        </div>                        
                        <div class="col-sm-12  " >
                            <a class="pull-right btn  btn-warning" id="btn-tambah" title="Tambah File" <?= $detail[0]->file_type==1?"":"style='display:none'"?> ><i class="fa fa-plus"></i></a>
                        </div>
                    </div>
                    <div class="row scrolling">
                        <?php foreach ($detail as $key => $value) { ?>
                        <div class="col-sm-12 form-group row group-gambar" id="div-upload-<?= $key ?>" >
                            <div class="col-sm-12 form-group" >                                       
                                <label class="input-group-text" for="inputGroupFile01">Upload File <span class="wajib">*</span></label>        
                                <div class="form-inline "  style="padding-bottom:10px;" >                                   
                                    <input type="file" class="form-control file-upload group-upload" id="fileUpload_<?= $key ?>" name="fileUploadUpdate[<?= $key ?>]"   style="width:90%" <?= $value->file_type==1?"accept='.jpg, .jpeg, .png'":"accept='.mp4'" ?> onchange="myData.viewImg(<?= $key ?>)">
                                    <?php $hideBtnDel = $value->file_type==1?"":"display:none" ?>

                                    <a class=" btn  btn-danger pull-right btn-del" id="btn-hapus" title="Hapus File" onclick="hapus(<?= $key ?>)" style="<?= $hideBtnDel; ?>">

                                        <i class="fa fa-trash-o"></i>
                                    </a>       
                                </div>                                                                                                                    
                            </div>
                            <!-- <div class="col-sm-6 form-group">
                                <label>Nama<span class="wajib">*</span></label>
                                <input type="text" name="name[<?= $key ?>]" class="form-control group-upload"  placeholder="nama" required value="<?= $value->name ?>" >
                            </div>                                                 -->
                            <div class="col-sm-6 form-group">
                            
                                <input type="hidden" name="name[<?= $key ?>]"  required value="<?= $value->name ?>" >

                                <label>Keterangan<span class="wajib">*</span></label>
                                <input type="text" name="desc[<?= $key ?>]" class="form-control group-upload"  placeholder="Keterangan" required value="<?= $value->desc ?>">                                

                                <input type="hidden" name="id[<?= $key ?>]"  required value="<?= $value->id ?>">
                                <input type="hidden" name="path[<?= $key ?>]"  required value="<?= $value->path ?>">
                            </div>       

                            <?php if($value->file_type==1) { ?>                            
                            <div class="col-sm-6 img-detail" id="img-detail-<?= $key ?>"> 
                                <span id="file_<?= $key ?>" >
                                    <img  src="<?= base_url($value->path) ?>" width="90%"; height="auto"   />
                                </span>
                            </div>
                            <?php } else {?>
                                <div class="col-sm-6 img-detail" id="img-detail-<?= $key ?>"> 
                                    <span id="file_<?= $key ?>" >
                                        <video id="file_<?= $key ?>" width="90%" height="auto" controls>
                                            <source src="<?= base_url($value->path) ?>" type="video/mp4">
                                        </video>
                                    </span>
                                </div>                                
                            <?php } ?>                            
                            <div class="col-sm-12 "><h1><hr /></h1></div>              
    
                        </div>              
                        <?php } ?>
                        <div class="col-sm-12" id="footerFile"> </div>
                    </div>
                </div>
            </div>

            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

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

        let idx=$(".group-gambar").length;
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
            let id = $(`#fileType option:selected`).data("id");
            if(id == 1)
            {
                $(`#btn-tambah`).slideDown();
                $(`.group-gambar`).slideDown();
                $(`.btn-del`).show();

                $('.group-upload').each(function(i, obj) {
                    $(`#${obj.id}`).attr("accept", ".jpg, .jpeg, .png")
                });
            }
            else if(id==2)
            {
                $(`#btn-tambah`).slideUp();
                $(`.group-gambar`).slideDown();
                $(`.btn-del`).hide();

                $('.group-upload').each(function(i, obj) {
                    $(`#${obj.id}`).attr("accept", ".mp4")
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

            $(`.img-detail`).html("");
       
        })
    })
</script>