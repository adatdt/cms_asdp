<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor4/ckeditor.js" type="text/javascript"></script>

<style type="text/css">
    .wajib{ color:red; }
    .switch {
        position: relative;
        display: block;
        width: 90px;
        height: 34px;
    }

    .switch input {display:none;}

    .slidertes {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc; /*#ca2222;*/
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px !important;
    }

    .slidertes:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50% !important;
    }

    input:checked + .slidertes {
        background-color: #3598dc;
    }

    input:focus + .slidertes {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slidertes:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(55px);
    }

    /*------ ADDED CSS ---------*/
    .slidertes:after
    {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 50%;
        font-size: 12px;
        font-family: "Open Sans", sans-serif;
    }

    input:checked + .slidertes:after
    {  
        content:'ON';
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Firefox */
    input[type=number] {
      -moz-appearance: textfield;
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
            <?php echo form_open('news/pushNotification/action_add', 'id="ff"  autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-8">
                            <input type="hidden" name="id" id="id" >
                            <input type="hidden" name="oldPath"  id="image_link" >

                            <div class="col-sm-6 form-group">
                                <label>Grup<span class="wajib">*</span></label>
                            
                                <select class="form-control select2 type" id="type" name="type">
                                  <option value=0>Pilih</option>                        
                                  <option value=1>Info</option>
                                  <option value=2>Promo</option>
                                  <option value=3>Berita</option>
                                </select>
            
                            <!-- <?= form_dropdown("type",$getDataType,"",' class="form-control select2"  id="grup" name="grup" required  ') ?> -->

                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Judul Berita/ Promo <span class="wajib">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Judul Berita/ Promo" required autocomplete="off">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Uraian<span class="wajib">*</span></label>
                                <?= form_dropdown("uraian","","",' class="form-control select2" id="uraian" required  ') ?> 
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Sub Judul Berita/ Promo <span class="wajib">*</span></label>
                                <input type="text" name="subTitle"  id="subTitle" class="form-control" placeholder="Sub Judul Berita/ Promo" required autocomplete="off">
                            </div>
                        
                            <div class="col-sm-12 "></div>

                            <div class="col-sm-4 form-group">
                                <label>Awal Publikasi <span class="wajib">*</span></label>
                                <input type="text" name="startDate" id="startDate" class="form-control" placeholder="Awal Publikasi" required autocomplete="off" readonly>
                                <div id="startDateError"></div>
                            </div>                       
                            <div class="col-sm-4 form-group">
                                <label>Akhir Publikasi<span class="wajib">*</span></label>
                                <input type="text" name="endDate" id="endDate" class="form-control" placeholder="Akhir Publikasi" required autocomplete="off" readonly>
                                <div id="endDateError"></div>
                            </div> 

                            <div class="col-sm-12 "></div>

                            <div class="col-md-7 form-group">
                                <label>Frekuensi <span class="wajib">*</span></label>
                                <div class="input-group ">
                                    <input type="number" id="frekuensi" name="frekuensi" min="1" oninput="this.value = Math.round(this.value);" max="<?=$paramMaxFrekuensi?>" class="form-control" placeholder="Input Frekuensi" required autocomplete="off" >
                                    <span class="input-group-addon btn-primary" type="hidden"  id="addFrekuensi"  style="background-color : #3699ff; color: white; cursor: pointer;" >Tambahkan Frekuensi</span>
                                </div>
                                <div id="frekuensiError"></div>         
                            </div>
                        
                            <div class="col-sm-12 "></div>  

                            <div id="kontenText">     
                            </div>
                       
                            <div class="col-sm-12 "></div>  

                            <div class="col-md-8 form-group" style="margin-bottom: -4px;">
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
                                </div>
                                    <input id = "fileHide" name = "fileHide" type= "hidden" />
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
                            <div class="col-sm-12 form-group" id="viewImage">   
                                <label>Gambar</label>
                                <!-- <center>Tidak Ada Gambar</center> -->
                                <img id="image" width="100%"/>
                            </div>
                        </div> 
                        

                    <!-- <div class="col-sm-4">

                            <div class="col-sm-12 form-group">
                                <label>Gambar <span class="wajib">*</span></label>
                                <?php 

                                    $img='';
                                    if(!empty('<img id="image" />'))
                                    {
                                        $img='<img id="image" width="100%"/>';
                                    } 
                                    
                                    echo $img;
                                ?>
                            </div>                                                   

                    </div> -->

                        <div class="col-sm-12 form-group">
                            <label>Konten <span class="wajib">*</span></label>
                            <input type="text" name="contentData" id="contentData" class="form-control" placeholder="Konten" required autocomplete="off">
                        </div>
                        <div class="col-sm-12 form-group">
                            <span class="wajib"><i>Awal publikasi adalah awal jam tanggal tersebut dan akhir publikasi jam akhir tanggal tersebut*</i></span>
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

         rules =  {
            title: { maxlength: 255},
        }

        messages= {
            title: { 
                maxlength: jQuery.validator.format("Maximal {0} Karater")
            }
        }

        myData.ckEditorConfig('contentData')

        myData.validateForm('#ff',function(url,data){
            
            postData2(url,data);
        });
        
        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });        

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
            var endDateValue = $('#endDate').val();

            someDate.getDate();
            someDate.setMonth(someDate.getMonth() + 1);
            someDate.getFullYear();
            let endDate = myData.formatDate(someDate);

            console.log (endDate)
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

            if(!endDateValue){
                $('#endDate').val(startDate).datepicker("update")
            }

            // $('#endDate').val(startDate).datepicker("update")
        });

        $("#addFrekuensi").click(function(e) {

            //Append new field
            let getValue = $('#frekuensi').val();
            let index_id = getValue ;
            let maxName = $("#frekuensi").attr("max");

            if (parseInt(getValue) > parseInt(maxName)){ 

                $("#addFrekuensi").prop("disabled",false);
                toastr.error('Nilai tidak boleh melebihi '+ maxName +' frekuensi');

            }else{

                let newInput =""
                for (let i = 0; i < getValue; i++) {

                   e.preventDefault();
                 newInput += `
                            <div id="inputFormRow${index_id}">
                                <div class="col-md-4 form-group">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <label>Jam<span class="wajib">*</span></label>
                                             
                                                <div id="kontenText" class="controls">
                                                 <div class="input-group ">
                                                    <?= form_dropdown("time[]",$getDataTime,"",' class="form-control select2"  id="time" required  ') ?>
                                                    <span class="input-group-addon btn-danger" onClick="divFunction(${index_id})" id="remove" style="background-color : #dc3545; color: white; cursor: pointer;">Hapus</span>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            
                             `;
                 index_id++

                } $("#kontenText").html(newInput);


                $(document).ready(function(){
                $('.select2').select2();
                });

            }
  
        });

        let dataDetail = [];
        $('#type').change(function() {
          var value = $(this).val();

          // console.log(value)

              if (value > 0 ) {

                  $.ajax({
                        url         : `<?= site_url()?>news/pushNotification/getDataUraian`,
                        // data        :"type="+value,
                        // data        : {"selected":value},
                        data :{type:value,
                            <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
                        },
                        type        : 'POST',
                        // enctype: 'multipart/form-data',
                        // processData: false,  // Important!
                        // contentType: false,
                        // cache:false,
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
                        
                        let uraian =`<option value="">Pilih</option>`

                        for (var i = 0 ; i < json.data.length; i++) {
                            uraian += ` <option value="${i}" >${json.data[i].title}</option>`
                        } 
                        $("#uraian").html(uraian);
                        // console.log (uraian)
                        dataDetail = json.data ;
                         
                        },            
                        error: function() {
                            toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                        },            
                        complete: function(){
                            $('#box').unblock(); 
                        },
                        "fnDrawCallback": function(allRow) 
                        {
                            let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                            let getToken = allRow.json[getTokenName];
                            csfrData[getTokenName] = getToken;
                            $.ajaxSetup({
                                data: csfrData
                            });
                        }
                    });

             }else{

                  $("#uraian").html(uraian);
                  $("#subTitle").val("");
                  $("#title").val("");
                  $("#startDate").val("");
                  $("#endDate").val("");
                  $("#image").attr("src","");
                  CKEDITOR.instances['contentData'].setData("");

             }
          
        });

        $('#uraian').on('change',function(e) {
            const value =$(this).val(); 
            // console.log (value)

            if (value == "" ) {
                $("#subTitle").val("");
                $("#title").val("");
                $("#startDate").val("");
                $("#endDate").val("");
                $("#image").attr("src","");
                CKEDITOR.instances['contentData'].setData("");
            }else{

            
            const id            = dataDetail[value].id ;
            const image         = dataDetail[value].image ;                  
            const dataType      = dataDetail[value].type ;
            const subTitle      = dataDetail[value].sub_title ;
            const title         = dataDetail[value].title;
            // const startDate     = dataDetail[value].start_published;
            // const endDate       = dataDetail[value].end_published;
            const contentData   = dataDetail[value].content;
            
            $("#id").val(id);
            $("#dataType").val(dataType);
            $("#subTitle").val(subTitle);
            $("#title").val(title);

            if (image === null) { 
                $("#image").attr("src",dataDetail[value].image);
                $("#image_link").val(dataDetail[value].image);
            }else{
                $("#image").attr("src",dataDetail[value].image.detail);
                $("#image_link").val(dataDetail[value].image.detail);
            }

            // $("#startDate").val(startDate);
            // $("#endDate").val(endDate);
            CKEDITOR.instances['contentData'].setData(contentData);
        }

        });

        function postData2(url,data){
        
            form = $('form')[0];
            formData = new FormData(form);            
            formData.append('contentData', myData.replaceStyle(CKEDITOR.instances.contentData.getData())); // add data to form data 

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
        let getValue =parseInt($('#frekuensi').val());
        let input = '#inputFormRow';
        let id_data = input+id;
        let valueCount = getValue-1;
        // console.log(valueCount)

        if(valueCount>0){ 
            // frekuensi-=1
            $(id_data).remove();      
            $("#frekuensi").val(valueCount);
        }
               
        })
    }
 
</script>
