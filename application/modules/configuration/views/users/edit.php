<style type="text/css">
     .wajib{
        color: red;
     }
 </style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('configuration/users/action_edit', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Username <span class="wajib">*</span></label>
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <input type="hidden" name="username"  value="<?php echo $row->username ?>">
                            <input type="text" name="username2" class="form-control" placeholder="Username" required value="<?php echo $row->username ?>" disabled>
                        </div>

                        <div class="col-sm-4">
                            <label>User Grup <span class="wajib">*</span> </label>
                            <?php echo form_dropdown('user_group', $user_group, $selectedGroup, 'class="form-control select22" required data-placeholder="Pilih User Grup" '); ?>
                        </div>

                        <div class="col-sm-4">
                            <label>Nama Depan <span class="wajib">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="Nama Depan" required value="<?php echo $row->first_name ?>">
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4">
                            <label>Nama Belakang</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Nama Belakang" value="<?php echo $row->last_name ?>">
                        </div>


                        <div class="col-sm-4">
                            <label>Pelabuhan <span class="wajib">*</span> </label>
                            <select class="form-control select22"  required name="port" id="port">
                                <option value="all">SEMUA PELABUHAN</option>
                                <?php foreach($port as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id)?>" <?php echo $value->id==$row->port_id?"selected":""?> ><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label>No Handphone</label>
                            <input type="text" name="phone" class="form-control" placeholder="No Handphone" onkeypress="return isNumberKey(event)" minlength="10"  value="<?php echo $row->phone ?>">
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email"  value="<?php echo $row->email ?>">
                        </div>              

                        <div class="col-sm-4">
                            <label>Username phone selfservice</label> 
                            <?= form_dropdown("userPhoneselfService",array(""=>"Pilih"),"",' class="form-control select22" id="userPhoneselfService" '); ?>

                        </div>

                        <div class="col-sm-4">
                            <label>Password phone selfservice</label>
                            <input type="text" name="passwordPhoneSelfservice" id="passwordPhoneSelfservice"   class="form-control" placeholder="Password phone selfservice"  value="<?php echo $row->password_phone ?>" disabled>
                        </div>             
                            
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4">
                            <label>Ext phone selfservice</label>
                            <input type="text" name="extPhoneSelfservice" id="extPhoneSelfservice" class="form-control" placeholder="Ext phone selfservice" value="<?php echo $row->extension_phone ?>"  disabled>
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
                                    <input type="checkbox" class="allow" name='login_cms' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $row->admin_pannel_login==true?"checked":""; ?> >Login CMS &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_pos' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $row->pos_login==true?"checked":""; ?>>Login POS &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>      
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_validator' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $row->validator_login==true?"checked":""; ?>>Login Mobile &nbsp;&nbsp; &nbsp; 
                                </div>
                            </div>
                        </div>      
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_ektp_reader' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $row->e_ktp_reader_login==true?"checked":""; ?>>E-KTP Reader &nbsp;&nbsp;
                                </div>
                            </div>
                        </div>      
                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='login_cs' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $row->cs_login==true?"checked":""; ?>>Login CS &nbsp;&nbsp; 
                                </div>
                            </div>
                        </div>      

                        <div class="col-sm-2 ">
                            <div class="input-group">
                                <div class="icheck-inline ">
                                    <input type="checkbox" class="allow" name='vertifikator' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $row->verifier_login==true?"checked":""; ?>>Verifikator &nbsp;&nbsp; 
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
                                <input type="checkbox" class="allow" name='command_center' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $row->command_center_login==true?"checked":""; ?> >Commad Center &nbsp;&nbsp;         
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
                
            
            </div>
            <?php echo createBtnForm('Update') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.mfp-wrap').removeAttr('tabindex')
        // $('.select2').select2()
        // $('.select2').change(function(){
        //     $(this).valid();
        // })
        $('.select22:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });        

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        getExt(1);
        $("#port").on("change", function(){
            getExt(2);
        })
        $(`#userPhoneselfService`).on("change", function()
        {
            const pass = $(this).find(':selected').data('pass');
            const ext = $(this).find(':selected').data('ext');
            $(`#extPhoneSelfservice`).val(ext);
            $(`#passwordPhoneSelfservice`).val(pass);

        })                    

    })

    function getExt(idt)
    {
        $.ajax({
                url : `<?= site_url() ?>configuration/users/getextension`,
                data : `portId=${$(`#port`).val()}&usernameExt=<?= $row->username_phone; ?>`,
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
                        let selectdata ="";
                        if(x[key].id == data.selectData)
                        {
                            selectdata =" selected";
                        }
                        userPhoneselfServiceHtml +=`<option value="${x[key].id}" data-pass="${x[key].password_phone}" data-ext="${x[key].extension_phone}" ${selectdata}>${x[key].username_phone} (${x[key].port_name})</option>`;
                    }
                    $(`#userPhoneselfService`).html(userPhoneselfServiceHtml);
                    if(idt != 1)
                    {
                        $(`#extPhoneSelfservice`).val("");
                        $(`#passwordPhoneSelfservice`).val("");
                    }
                },
                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },
                complete: function(){
                    $('#box').unblock(); 
                }                
            })          
    }

</script>