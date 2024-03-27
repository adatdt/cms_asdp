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
            <?php echo form_open('configuration/users/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Username <span class="wajib">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
<!--                         <div class="col-sm-4">
                            <label>Password <span class="wajib">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div> -->
                        <div class="col-sm-4">
                            <label>User Grup <span class="wajib">*</span></label>
                             <?php echo form_dropdown('user_group', $user_group, '', 'class="form-control select2" required data-placeholder="Pilih User Grup"'); ?>
                                
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
                            <select class="form-control select2"  required name="port">
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
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 ">
                            <label>Pilih Login</label>
                            <div class="input-group">
                                <div class="icheck-inline">

                                    <input type="checkbox" class="allow" name='login_cms' data-checkbox="icheckbox_flat-grey" value="1" >Login CMS &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='login_pos' data-checkbox="icheckbox_flat-grey" value="1" >Login POS &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='login_validator' data-checkbox="icheckbox_flat-grey" value="1" >Login Mobile &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='login_ektp_reader' data-checkbox="icheckbox_flat-grey" value="1">E-KTP Reader &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='login_cs' data-checkbox="icheckbox_flat-grey" value="1">Login CS &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='vertifikator' data-checkbox="icheckbox_flat-grey" value="1">Verifikator &nbsp;&nbsp; 

                                
                                </div>
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
        $('.mfp-wrap').removeAttr('tabindex')
        $('.select2').select2()
        $('.select2').change(function(){
            $(this).valid();
        })

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

    })
</script>