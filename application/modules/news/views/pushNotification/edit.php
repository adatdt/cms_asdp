<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor4/ckeditor.js" type="text/javascript"></script>

<style type="text/css">
    .wajib{ color:red; }
    .input-group-icon{
    display: table;
    border-collapse: separate;
    }

    .fileinput-filename {
    display: inline-block;
    max-width: 600px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    vertical-align: middle;
    }
    
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('news/pushNotification/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-8">
                            <input type="hidden" name="type" id="type" value="<?= $detail->type ?>"  >
                            <input type="hidden" name="push_notification_id" id="push_notification_id"  value="<?= $detail->id ?>" >
                            <input type="hidden" name="notification_id" id="notification_id" value="<?= $detail->notification_id ?>">

                            <div class="col-sm-6 form-group">
                                <label>Grup<span class="wajib">*</span></label>
                                <input type="text" name="type_grup" class="form-control" placeholder="Grup" required autocomplete="off" value="<?= $type_grup ?>"  readonly>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Judul Berita <span class="wajib">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Judul Berita/ Promo" required autocomplete="off" value="<?= $detail->title ?>" maxlength="255" >

                                <input type="hidden" name="id"  value="<?= $this->enc->encode($detail->id) ?>" 
                                >                                         
                                <input type="hidden" name="oldPath"  value="<?= $detail->image?>" >
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Uraian<span class="wajib">*</span></label>
                                 <input type="text" name="title_old" class="form-control" placeholder="Uraian" required autocomplete="off" value="<?= $detail->title_old ?>" readonly>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Sub Judul Berita <span class="wajib">*</span></label>
                                <input type="text" name="subTitle" class="form-control" placeholder="Sub Judul Berita/ Promo" required autocomplete="off" value="<?= $detail->sub_title ?>">
                            </div>
                            
                            <div class="col-sm-12 "></div>

                            <div class="col-sm-6 form-group">
                                <label>Awal Publikasi <span class="wajib">*</span></label>
                                <input type="text" name="startDate" id="startDate" class="form-control" placeholder="Awal Publikasi" required autocomplete="off" readonly value="<?= date('Y-m-d ', strtotime($detail->start_published))?>">

                                <div id="startDateError"></div>
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Akhir Publikasi <span class="wajib">*</span></label>
                                <input type="text" name="endDate" id="endDate" class="form-control" placeholder="Akhir Publikasi" required autocomplete="off" readonly value="<?= date('Y-m-d ', strtotime($detail->end_published)) ?>">
                                <div id="endDateError"></div>
                            </div>                        

                            <div class="col-sm-12 "></div>

                            <div class="col-md-4 form-group">
                                <div class="input-group pad-top">
                                        <button type="button" class="btn btn-primary mt-ladda-btn ladda-button" max="<?=$paramMaxFrekuensi?>" data-style="zoom-in" id="editFrekuensi">
                                            <span class="ladda-label">Tambah Frekuensi</span>
                                            <span class="ladda-spinner"></span>
                                        </button>
                                </div>
                            </div>

                            <div class="col-sm-12 "></div>  

                            <div id="kontenText">

                            <?php $i=1;  foreach ($detail->time_published as $time ) { ?> 

                                <div id="inputFormRow<?=$i?>">
                                    <div class="col-md-4 form-group">
                                        <label>Jam<span class="wajib">*</span></label> 
                                        <div class="input-group-icon">
                                             <?= form_dropdown("time[]",$getDataTime,$time,' class="form-control select2" style="cursor: pointer;" id="time" ') ?>
                                                <span class="input-group-addon btn-danger" onClick="divFunction(<?=$i;?>)" id="remove" style="background-color : #dc3545; color: white; cursor: pointer;">Hapus</span>
                                        </div>
                                    </div>
                                </div>
                            
                            <?php $i++; }?>
        
                            </div>
                       
                            <div class="col-sm-12 "></div>  
                        
                            <div class="col-md-8 form-group"  style="margin-bottom: -4px;" >
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <label>Thumbnail </label>
                                    <div class="input-group ">

                                        <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file" style="background-color: #3699ff !important; color: white;">
                                            <span class="fileinput-new"> Pilih File </span>
                                            <span class="fileinput-exists"> Pilih File</span>
                                            <input type="hidden"><input type="file" name="thumbnail" id="thumbnail" accept="image/jpg, image/jpeg, image/png"> </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Hapus </a>
                                    </div>
                                    <input id = "fileHide" name = "fileHide" type= "hidden" />
                                </div>
                            </div>
                            <div class="col-sm-12 form-group" style="margin-bottom: -4px;">
                                <span class="wajib"><i>- Format file yang di perbolehkan JPG/JPEG atau PNG*</i></span>
                            </div> 
                            <div class="col-sm-12 form-group" style="margin-bottom: -4px;">
                                <span class="wajib"><i>- File Tidak boleh lebih dari <?=$paramMaxSize?> kb*</i></span>
                            </div>
                            <div class="col-sm-12 form-group">
                                <span class="wajib"><i>- File yang dianjurkan 3:4 Landscape*</i></span>
                            </div>  
                        </div>

                        <div class="col-sm-4">

                            <div class="col-sm-12 form-group">
                                <label>Gambar <span class="wajib">*</span></label>
                                <?php 

                                    $img='<center>Tidak Ada Gambar</center>';
                                    if(!empty($detail->image))
                                    {
                                        $img='<img src="'.$detail->image.'" width="100%">';
                                    } 
                                    
                                    echo $img;
                                ?>
                            </div>                                                   

                        </div>

                        <div class="col-sm-12 form-group">
                            <label>Konten <span class="wajib">*</span></label>
                            <textarea type="text" name="contentData" id="contentData" class="form-control" placeholder="Konten" required autocomplete="off"> <?= $detail->content ?></textarea>
                        </div> 

                        <div class="col-sm-12 form-group">
                            <span class="wajib"><i>Awal publikasi adalah awal jam tanggal tersebut dan akhir publikasi jam akhir tanggal tersebut*</i></span>
                        </div>                                                


                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    let countFrekuensi= parseInt(`<?= $detail->frequency?>`) ;
    let index_id = countFrekuensi+1 ;
    // console.log (index_id)

    $(document).ready(function(){
        rules =  {
            title: { maxlength: 255}
        }

        messages= {
            title: { 
                maxlength: jQuery.validator.format("Maximal {0} Karater")
            }
        }

        validateForm('#ff',function(url,data){
            
            postData2(url,data);
        });

        myData.ckEditorConfig('contentData') 

       $('#startDate').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });

        $('#endDate').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
            startDate: new Date()
        });

        $("#startDate").change(function() {

            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth() + 1);
            someDate.getFullYear();
            let endDate = myData.formatDate(someDate);

            // destroy ini firts setting
            $('#endDate').datepicker('remove');

            // Re-int with new options
            $('#endDate').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            // $('#endDate').val(startDate).datepicker("update")
        });



        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#editFrekuensi").click(function () {

            let newInput =""
            let maxName = $("#editFrekuensi").attr("max");
            countFrekuensi+=1

            if(countFrekuensi <= parseInt(maxName)){
                 newInput += `
                      <div id="inputFormRow${index_id}">
                         <div class="col-md-4 form-group">
                            <label>Jam<span class="wajib">*</span></label> 
                            <div class="input-group-icon">
                                 <?= form_dropdown("time[]",$getDataTime,"",' class="form-control select2" style="cursor: pointer;" id="time" required  ') ?>
                                    <span class="input-group-addon btn-danger" onClick="divFunction(${index_id})" id="remove" style="background-color : #dc3545; color: white; cursor: pointer;">Hapus</span>
                            </div>
                        </div>
                     </div>`;

             $('#kontenText').append(newInput);
             index_id++

            }else {
              countFrekuensi -= 1 
              toastr.error('Jumlah Jam harus kurang dari '+ maxName +' frekuensi');
            }

            $(document).ready(function(){
                $('.select2').select2();
            });

        });

        // $(document).on('click', '#remove', function () {

        // });

        function postData2(url,data){
        
            form = $('form')[0];
            formData = new FormData(form);            
            // formData.append('contentData', myData.replaceStyle(CKEDITOR.instances.contentData.getData())); // add data to form data 
            formData.set('contentData', myData.replaceStyle(CKEDITOR.instances.contentData.getData())); // add data to form data 

            formData.set("fileHide", btoa($('[name=fileHide]').val()));

            $.ajax({
                url         : url,
                data        :formData,
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
                // data        :data,
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
                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });
                    
                    if(json.code == 1){
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');
            
                        $('#dataTables').DataTable().ajax.reload( null, false )
            
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
        }

        $('#thumbnail').change(function (e) {
            const img = e.target.files[0]; 
            const maxFile =`<?= $parameter ?>`        
            if(img !== undefined )
            {                
                myData.resizeFile(img, maxFile);
            }
        });        
    })


function divFunction(id){

    $(document).ready(function(){

    let input = '#inputFormRow';
    let id_data = input+id;
    // console.log(id_data)
        if(countFrekuensi>1){ 
            countFrekuensi-=1
            $(id_data).remove();
        }
            
    })
}


</script>
