<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor4/ckeditor.js" type="text/javascript"></script>

<style type="text/css">
    .wajib{ color:red; }

    .fileinput-filename {
    display: inline-block;
    max-width: 600px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    vertical-align: middle;
    }

    #mytable th {
        font-weight: normal;
    
    }
    #mytable td {
        font-size:12px;
        padding-right: 3%;
        padding-top: 2%;

    }
    #mytable td.spec {
        text-align: center;
    }
    #mytable td.alt {
        text-align: left;
    }

  
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('news/newsPromo/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-8 form-group">
                            <!-- <div class="col-sm-6 form-group">
                                <label>Tipe<span class="wajib">*</span></label>
                                <?= form_dropdown("type",$getDataType,$getDataTypeSelected,' class="form-control select2" required  ') ?>
                            </div> -->
                            <div class="col-sm-6 form-group">
                                <label>Urutan <span class="wajib">*</span></label>
                                <input type="number" min=1 name="ordering" class="form-control" placeholder="urutan" required autocomplete="off" value="<?= $detail->order ?>" >
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Judul Berita Promo<span class="wajib">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Judul Berita/ Promo" required autocomplete="off" value="<?= $detail->title ?>" maxlength="255" >

                                <input type="hidden" name="id"  value="<?= $this->enc->encode($detail->id) ?>" 
                                >                                         
                                <input type="hidden" name="oldPath"  value="<?= $detail->image->detail ?>" >
                                <input type="hidden" name="startDateTime"  value="<?= date('Y-m-d H:i', strtotime($detail->start_published)); ?>" >                                           
                            </div>

                            <div class="col-sm-12 "></div>

                            <!-- <div class="col-sm-6 form-group">
                                <label>Awal Publikasi <span class="wajib">*</span></label>
                                <input type="text" name="startDate" id="startDate" class="form-control" placeholder="Awal Publikasi" required autocomplete="off" readonly value="<?= date('Y-m-d H:i', strtotime($detail->start_published)); ?>">
                                <div id="startDateError"></div>
                            </div> -->

                            <div class="col-sm-6 form-group">
                                <label>Awal Publikasi <span class="wajib">*</span></label>

                                <?php if(date('Y-m-d H:i', strtotime($detail->start_published)) <= date('Y-m-d H:i') ): 
                                    $typeStartDate = 2;    
                                    ?>
                                    <input type="text" name="startDate2" style="background-color: #eee !important; cursor: no-drop;" class="form-control" placeholder="Awal Publikasi" required="" autocomplete="off" readonly aria-required="true" value="<?= date('Y-m-d H:i', strtotime($detail->start_published)); ?>">
                                    <input type="hidden" name="startDate" value="<?= date('Y-m-d H:i', strtotime($detail->start_published)); ?>" > 
                                <?php else: 
                                    $typeStartDate = 1; 
                                    ?>
                                   <input type="text" name="startDate" id="startDate" class="form-control" placeholder="Awal Publikasi" required="" autocomplete="off" readonly aria-required="true" value="<?= date('Y-m-d H:i', strtotime($detail->start_published)); ?>">
                                <?php endif; ?>
                                <input type ="hidden" name="typeStartDate" value="<?= $typeStartDate ?>" >

                                <div id="startDateError"></div>
                            </div> 

                            <div class="col-sm-6 form-group">
                                <label>Akhir Publikasi <span class="wajib">*</span></label>
                                <input type="text" name="endDate" id="endDate" class="form-control" placeholder="Akhir Publikasi" required autocomplete="off" readonly value="<?= date('Y-m-d H:i', strtotime($detail->end_published)) ?>">
                                <div id="endDateError"></div>
                            </div>                        

                            <div class="col-sm-12 "></div>
                            <!-- <div class="col-sm-6 form-group ">
                                <label>is direct </label>
                                <label class="switch" >
                                    <input type="hidden" name="is_direct" value="0">
                                    <input type="checkbox" name="is_direct" id="togBtn" value="1"  <?= $detail->is_redirect==1?"checked":"" ?> ><div class="slidertes round pull-right "></div>
                                </label>                             
                            </div>
                            -->
                            <div class="col-md-12 form-group" style="margin-bottom: -4px;">
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
                                    <input id = "fileHideThumbnail" name = "fileHideThumbnail" type= "hidden" />
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
                            <div class="col-md-12 form-group">
                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-primary mt-ladda-btn ladda-button add-url-image" max="5" data-style="zoom-in" id="addRow" >
                                        <span class="ladda-label"><i class="fa fa-plus"></i> Tambah URL Gambar</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-sm-12 form-group ">
                                <table id="mytable"  align="left" cellspacing="0"> 
                                    <tr class="topRow"> 
                                        <th width="5" scope="col"></th> 
                                        <th width="800" >Link URL Gambar</th>  
                                        <th width="20"></th>
                                    </tr> 
                        
                                    <?php 
                                        if(!empty($detail->content_images)){
                                            $i=1;  foreach ($detail->content_images as $linkImages ) { ?> 
                                                <tbody>
                                                    <tr>
                                                        <td scope="row" class="spec" id="row_num<?=$i;?>">
                                                            <input type="hidden"  class="form-control imageOrder" name="imageOrder[]" id="<?=$i;?>"  value="<?=$i;?>" readonly>
                                                        </td>
                                                        <td class="alt"> 
                                                            <input type="text" class="form-control" name="linkImages[]" value="<?=$linkImages;?>" >
                                                        </td>
                                                        <td>
                                                            <button type="button" name="remove" class="btn btn-md btn-danger pull-left btn_remove"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>                    
                                            <?php $i++; }
                                        }
                                    ?>
                                </table>
                            </div>

                        </div>
                        <div class="col-sm-4 ">
                            <div class="col-sm-12 form-group">
                                <label>Sub Judul Berita Promo<span class="wajib">*</span></label>
                                <input type="text" name="subTitle" class="form-control" placeholder="Sub Judul Berita Promo" required autocomplete="off" value="<?= $detail->sub_title ?>">
                            </div>
                            <div class="col-sm-12 form-group">
                                <label>URL Video </label>
                                <input type="text" id="videoUrl" name="videoUrl" class="form-control" placeholder="URL Video" autocomplete="off" value="<?= $detail->video ?>" >
                            </div>                                             
                            <div class="col-sm-12 form-group" id="viewImage">
                            <label>Gambar</label>
                                <img src="<?= $detail->image->detail ?>" width="100%"/>
                            </div>                            
                        </div>

                        <div class="col-sm-12 form-group">
                            <label>Konten <span class="wajib">*</span></label>
                            <textarea type="text" name="contentData" id="contentData" class="form-control" placeholder="Konten" required autocomplete="off"> <?= $detail->content ?></textarea>
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
    $(document).ready(function(){
        rules =  {
            title: { maxlength: 255},
            videoUrl: {url:true}
        }

        messages= {
            title: { 
                maxlength: jQuery.validator.format("Maximal {0} Karater")
            }
        }

        $("#videoUrl").on("keyup",function(){
            let link = $(this).val();
            if (link.indexOf("http://") == 0 || link.indexOf("https://") == 0) {
                
                $(this).rules("add", {messages : { url : 'Format Url Tidak Valid.' }});                
            }
            else{

                $(this).rules("add", {messages : { url : '(Domain url wajib diawali https://atau http://)' }});                
            }
        })           

        validateForm('#ff',function(url,data){
            postData2(url,data);
        });

        myData.ckEditorConfig('contentData')   

        $('#startDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            minuteStep:1,
            // startDate: new Date()
            startDate: getDataNow()
        });

        $('#endDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
            minuteStep:1,
            // startDate: new Date()
            startDate: getDataNow()
        });


        $("#startDate").change(function() {

            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth() + 1);
            someDate.getFullYear();
            let endDate = myData.formatDate(someDate);

            // destroy ini firts setting
            $('#endDate').datetimepicker('remove');

            // Re-int with new options
            $('#endDate').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                minuteStep:1,
                endDate: endDate,
                startDate: startDate
            });

            // $('#endDate').val(startDate).datetimepicker("update")
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        function postData2(url,data){
        
            form = $('form')[0];
            formData = new FormData(form);            
            // formData.append('contentData', myData.replaceStyle(CKEDITOR.instances.contentData.getData())); // add data to form data 
            formData.set('contentData', myData.replaceStyle(CKEDITOR.instances.contentData.getData())); // add data to form data 

            formData.set("fileHide", btoa($('[name=fileHide]').val()));
            formData.set("fileHideThumbnail", btoa($('[name=fileHideThumbnail]').val()));
        
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

        $('#startDateNow').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            startDate: getDataNow()
        });
        
        function getDataNow()
        {
            var d = new Date();
            var month = d.getMonth() +1;
            var day = d.getDate();
            var year = d.getFullYear();
            var hour = d.getHours();
            var paramMinutes = <?= $rangeMinutesFrekuensi ?>;
            var minutes = d.getMinutes() + paramMinutes;

            let getDay="";
            let getMonth="";

            if(day.length>1)
            {
                getDay=day;
            }
            else
            {
                getDay=`0${day}`
            }

            if(month.length>1)
            {
                getMonth=month;
            }
            else
            {
                getMonth=`0${month}`
            }            
            const returnData = `${year}-${getMonth}-${getDay} ${hour}: ${minutes}`;

            return returnData
        }

    })

    var array = document.querySelectorAll('.imageOrder');
    var total = 0;
    for (var i = 0; i < array.length; i++) {
        if (parseInt(array[i].value))
        total = parseInt(array[i].value);
    }

    if (total == 0){
        $(".topRow").hide();
    }

    var i = total; 

    $('#addRow').click(function() {
    i++;
        $('#mytable').append('<tbody><tr><td scope="row" class="spec" id="row_num' + i + '">' +
        '<input type="hidden" class="form-control imageOrder" name="imageOrder[]" id="' + i + '" value="' + i + '" readonly></td>' +
        '<td class="alt"> <input type="text" class="form-control" name="linkImages[]" value="" ></td>' +
        '<td><button type="button" name="remove" class="btn btn-md btn-danger pull-left btn_remove"><i class="fa fa-trash"></i></button></td></tr></tbody>');
        
        $('.topRow').show(); 
    });

    $(document).on('click', '.btn_remove', function() {
        $(this).closest("tr").remove(); 

        $('table tr').each(function(index) {
            $(this).find("td:eq(0)").html('<input type="hidden" class="form-control imageOrder" name="imageOrder[]" value="' + (index) + '" readonly>')
            
        });

        i--;

        if ($("#mytable > tbody > tr >td").length == 0){
            $(".topRow").hide();
        }

    });

</script>
