 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 <script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor4/ckeditor.js" type="text/javascript"></script>

 <style type="text/css">
     .wajib {
         color: red
     }
 </style>

 <div class="col-md-6 col-md-offset-3">
     <div class="portlet box blue" id="box">
         <?php echo headerForm($title) ?>
         <div class="portlet-body">
             <?php echo form_open('pids/settingParamPids/action_edit', 'id="ff" autocomplete="on"'); ?>
             <div class="box-body">
                 <div class="form-group">

                     <div class="row">

                         <div class="col-sm-6 form-group">
                             <label>Nama Param<span class="wajib">*</span></label>
                             <input type="text" name="name" class="form-control" placeholder="Nama Param" required value="<?= $detail->param_name ?>" disabled>
                             <input type="hidden" name="id" value="<?= $id ?>">
                         </div>

<!--                          <div class="col-sm-6 form-group">
                             <label>Value Param <span class="wajib">*</span></label>
                             <input type="text" name="value_param" class="form-control" placeholder="Value Param" required value="<?= $detail->param_value ?>">
                         </div> -->

                        <div class="col-sm-6 form-group">
                            <label>Tipe Input Value Param <span class="wajib">* </span></label>
                            <select class="form-control select2"  required name="typeInputValueParam" id="typeInputValueParam">
                                <!-- <option value="">Pilih</option> -->
                                <option value="file" <?= $detail->param_type_value=='file'?"selected":""; ?>>FILE</option>
                                <option value="html" <?= $detail->param_type_value=='html'?"selected":""; ?>>HTML</option>
                                <option value="text" <?= $detail->param_type_value=='text'?"selected":""; ?>>TEXT</option>
                            </select>
                        </div>                        


                         <div class="col-sm-12 form-group"></div>
                         <div class="col-sm-6 form-group">
                             <label>Tipe Param <span class="wajib">*</span></label>
                             <input type="text" name="tipe_param" class="form-control" placeholder="Tipe Param" required value="<?= $detail->param_type ?>">
                         </div>


                         <div class="col-sm-6 form-group">
                             <label>Info <span class="wajib">*</span></label>
                             <input type="text" name="info" class="form-control" placeholder="Info" required value="<?= $detail->info ?>">
                         </div>

                         <div class="col-sm-12 form-group"></div>

                         <div class="col-sm-6 form-group">
                             <label>Pelabuhan <span class="wajib">*</span></label>
                             <?= form_dropdown("port", $port, $portDataSelected, ' class="form-control select2" placeholder="Info" required ') ?>
                         </div>
                         <div class="col-sm-6 form-group" id="valueInput"><?= $paramTypeValue ?></div>  

                         <div class="col-sm-12 form-group"></div>

                         <div class="col-sm-12 form-group" id="valueHtml" ><?= $paramTypeValueHtml ?></div>    
                     </div>
                 </div>
             </div>
             <?php echo createBtnForm('Edit') ?>
             <?php echo form_close(); ?>
         </div>
     </div>
 </div>

 <script type="text/javascript">
     $(document).ready(function() {
         validateForm('#ff', function(url, data) 
         {
            if($("#typeInputValueParam").val()=='html')
            {
                var getValue=CKEDITOR.instances['value_param'].getData()
                data['value_param'] = getValue;

            }
             postData2(url, data);
         });


        function postData2(url,data,y){

            form = $('form')[0];
            formData = new FormData(form);
            if($("#typeInputValueParam").val()=='html')
            {
                formData.set('value_param', data.value_param);
            }

            $.ajax({
                url         : url,
                data        :formData,
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
                    if(json.code == 1)
                    {
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');

                        $('#dataTables').DataTable().ajax.reload( null, false );
                        socket.emit('pidsUpdateParams', parseInt(json.data['portId']));

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

        $("#typeInputValueParam").on("change",function(){

            let getVal=$(this).val();

            var html="";
            
            if(getVal=='file')
            {


                html    +=`<div class="fileinput fileinput-new" data-provides="fileinput">
                                <label>Pilih File gambar <span class="wajib">*</span></label>
                                <div class="input-group ">

                                    <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                        <span class="fileinput-filename"> </span>
                                    </div>
                                    <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new"> Pilih File </span>
                                        <span class="fileinput-exists"> Pilih File</span>
                                        <input type="hidden"><input type="hidden"><input type="file" name="file"  ></span>
                                      <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput" title="hapus"><i class='fa fa-trash'></i> </a>
                                </div>
                            </div>`

                $("#valueInput").html(html);
                $("#valueHtml").html("");

            }
            else if(getVal=='text')
            {
                html +=`
                            <label>Value Param <span class="wajib">*</span></label>
                            <input type="text" name="value_param" class="form-control" placeholder="Value Param" required>
                        `
                $("#valueInput").html(html);
                $("#valueHtml").html("");
            }
            else if(getVal=='html')
            {
                html    +=`                 
                    <div class="form-group">
                        <label>Value Param<span class="wajib">*</span></label>
                        <textarea class="wysihtml5 form-control" name="value_param" id="value_param" placeholder="Info" required  rows="20"></textarea>
                    </div>`

                $("#valueHtml").html(html);
                $("#valueInput").html("");
                CKEDITOR.config.extraPlugins = 'justify';
                CKEDITOR.config.height = '100px';
                CKEDITOR.replace('value_param', {
                    toolbarGroups: [{
                            name: 'clipboard',
                            groups: ['clipboard', 'undo']
                        },
                        {
                            name: 'editing',
                            groups: ['find', 'selection', 'spellchecker', 'editing']
                        },
                        {
                            name: 'forms',
                            groups: ['forms']
                        },
                        {
                            name: 'links',
                            groups: ['links']
                        },
                        {
                            name: 'insert',
                            groups: ['insert']
                        },
                        {
                            name: 'document',
                            groups: ['mode', 'document', 'doctools']
                        },
                        {
                            name: 'tools',
                            groups: ['tools']
                        },
                        '/',
                        {
                            name: 'basicstyles',
                            groups: ['basicstyles', 'cleanup']
                        },
                        {
                            name: 'colors',
                            groups: ['colors']
                        },
                        {
                            name: 'paragraph',
                            groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
                        },
                        {
                            name: 'styles',
                            groups: ['styles']
                        },
                        {
                            name: 'others',
                            groups: ['others']
                        },
                        {
                            name: 'about',
                            groups: ['about']
                        }
                    ],

                    removeButtons: 'Print,Preview,ExportPdf,NewPage,Save,Templates,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Font,Flash,BidiRtl,Language,ShowBlocks,BidiLtr'
                });                  
            }
            else
            {
                html +="";
                $("#valueInput").html(html);
                $("#valueHtml").html("");
            }            

        })


        if($("#typeInputValueParam").val()=='html')
        {
            CKEDITOR.config.extraPlugins = 'justify';
            CKEDITOR.config.height = '100px';
            CKEDITOR.replace('value_param', {
                toolbarGroups: [{
                        name: 'clipboard',
                        groups: ['clipboard', 'undo']
                    },
                    {
                        name: 'editing',
                        groups: ['find', 'selection', 'spellchecker', 'editing']
                    },
                    {
                        name: 'forms',
                        groups: ['forms']
                    },
                    {
                        name: 'links',
                        groups: ['links']
                    },
                    {
                        name: 'insert',
                        groups: ['insert']
                    },
                    {
                        name: 'document',
                        groups: ['mode', 'document', 'doctools']
                    },
                    {
                        name: 'tools',
                        groups: ['tools']
                    },
                    '/',
                    {
                        name: 'basicstyles',
                        groups: ['basicstyles', 'cleanup']
                    },
                    {
                        name: 'colors',
                        groups: ['colors']
                    },
                    {
                        name: 'paragraph',
                        groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
                    },
                    {
                        name: 'styles',
                        groups: ['styles']
                    },
                    {
                        name: 'others',
                        groups: ['others']
                    },
                    {
                        name: 'about',
                        groups: ['about']
                    }
                ],

                removeButtons: 'Print,Preview,ExportPdf,NewPage,Save,Templates,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Font,Flash,BidiRtl,Language,ShowBlocks,BidiLtr'
            });    
        }              


         $('.select2:not(.normal)').each(function() {
             $(this).select2({
                 dropdownParent: $(this).parent()
             });
         });
     })
 </script>