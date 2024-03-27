<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 <style type="text/css">
     .wajib{
        color: red;
     }
 </style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('configuration/users/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Username <span class="wajib">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <!--<div class="col-sm-4">
                            <label>Password <span class="wajib">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div> -->
                        <div class="col-sm-4">
                            <label>User Grup <span class="wajib">*</span></label>
                             <?php echo form_dropdown('user_group', $user_group, '', 'class="form-control select22" required data-placeholder="Pilih User Grup"'); ?>
                                
                        </div>

                        <div class="col-sm-4">
                            <label>No Handphone</label>
                            <input type="text" name="phone" class="form-control" placeholder="No Handphone" onkeypress="return isNumberKey(event)" minlength="10" >
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nama Depan <span class="wajib">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="Nama Depan" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Nama Belakang</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Nama Belakang" >
                        </div>

                        <div class="col-sm-4">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control js-data-example-ajax select22"  required name="port" id="port">
                                <option value="">Pilih</option>
                                <option value="all">SEMUA PELABUHAN</option>
                                <?php foreach($port as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id)?>"><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                       
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" >
                        </div>
                        
                        <div class="col-sm-4">
                            <label>Username phone selfservice</label>
                            <?= form_dropdown("userPhoneselfService",array(""=>"Pilih"),"",' class="form-control select22" id="userPhoneselfService" '); ?>
                        </div>

                        <div class="col-sm-4">
                            <label>Password phone selfservice</label>
                            <input type="text" name="passwordPhoneSelfservice" id = "passwordPhoneSelfservice" class="form-control" placeholder="Password phone selfservice" disabled>
                        </div>                        

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4">
                            <label>Ext phone selfservice</label>
                            <input type="text" name="extPhoneSelfservice" id="extPhoneSelfservice" class="form-control" placeholder="Ext phone selfservice" disabled>
                        </div>                        

                    </div>
                </div>                

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <label>Pilih Login</label>
                         </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_cms' data-checkbox="icheckbox_flat-grey" value="1" >Login CMS &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_pos' data-checkbox="icheckbox_flat-grey" value="1" >Login POS &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>      
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_validator' data-checkbox="icheckbox_flat-grey" value="1" >Login Mobile &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>      
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_ektp_reader' data-checkbox="icheckbox_flat-grey" value="1">E-KTP Reader &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>      
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_cs' data-checkbox="icheckbox_flat-grey" value="1">Login CS &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>      

                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                <input type="checkbox" class="allow" name='vertifikator' data-checkbox="icheckbox_flat-grey" value="1">Verifikator &nbsp;&nbsp;         
                                </div>
                            </div>
                        </div>                           

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                <input type="checkbox" class="allow" name='command_center' data-checkbox="icheckbox_flat-grey" value="1">Commad Center &nbsp;&nbsp;         
                                </div>
                            </div>
                        </div>                              
                    </div>
                </div>          

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 ">
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
       
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        $('.select22:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#port").on("change", function(){
            $.ajax({
                url : `<?= site_url() ?>configuration/users/getExtension`,
                data : `portId=${$(this).val()}`,
                type        : 'POST',
                dataType    : 'json',
                beforeSend: function(){
                    unBlockUiId('box')
                },
                success : function(data)
                {
                    const x = data.data;
                    let userPhoneselfServiceHtml = `<option value="" datal-pass="" data-ext="" >Pilih</option>`;

                    for (const key in x) {
                        userPhoneselfServiceHtml +=`<option value="${x[key].id}" data-pass="${x[key].password_phone}" data-ext="${x[key].extension_phone}" >${x[key].username_phone} (${x[key].port_name})</option>`;
                    }
                    $(`#userPhoneselfService`).html(userPhoneselfServiceHtml);
                    $(`#extPhoneSelfservice`).val("");
                    $(`#passwordPhoneSelfservice`).val("");
                },
                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },
                complete: function(){
                    $('#box').unblock(); 
                }                
            })            
        })

        $(`#userPhoneselfService`).on("change", function()
        {
            const pass = $(this).find(':selected').data('pass');
            const ext = $(this).find(':selected').data('ext');

            $(`#extPhoneSelfservice`).val(ext);
            $(`#passwordPhoneSelfservice`).val(pass);

        })
    })
</script>