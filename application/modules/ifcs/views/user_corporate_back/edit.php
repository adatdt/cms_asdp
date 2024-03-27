 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/user_corporate/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">

                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Corporate <span class="wajib">*</span></label>
                            <select name="corporate_name" id="corporate_name" class="form-control select2" required disabled>
                                <!-- <option value="">Pilih</option> -->
                                <?php foreach($corporate as $key=>$value ) { ?>
                                    <option value="<?php echo $value->corporate_code ?>" <?php echo $value->corporate_code==$detail->corporate_code?"selected":""; ?>><?php echo strtoupper($value->corporate_name) ?></option>
                                <?php } ?>
                            </select>

                            <input type="hidden" name="corporate" required value="<?php echo $id ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Corporate <span class="wajib">*</span></label>
                            <input type="text" name="code_coporate" class="form-control"  placeholder="Kode Coporate" required readonly value="<?php echo $detail->corporate_code ?>">
                        </div> 

                        <div class="col-sm-12 form-group"></div>                       

                        <div class="col-sm-6 form-group">
                            <label>Tipe User <span class="wajib">*</span></label>
                            <select name="member_type" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php foreach($member_type as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->member_type?"selected":""; ?> ><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>                                
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama" required value="<?php echo $detail->name ?>">
                        </div>

                        <div class="col-sm-12 form-group"></div>                        

                        <div class="col-sm-6 form-group">
                            <label>No. KTP <span class="wajib">*</span></label>
                            <input type="text" name="nik" class="form-control"  placeholder="No. KTP" required value="<?php echo $detail->nik ?>" onkeypress="return isNumberKey(event)" minlength="16" maxlength="16">
                        </div>                        

                        <div class="col-sm-6 form-group">
                            <label>No. Induk Kepegawaian <span class="wajib">*</span></label>
                            <input type="text" name="nip" class="form-control"  placeholder="No. Induk Kepegawaian" required value="<?php echo $detail->nip ?>">
                        </div>                        

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Telepon <span class="wajib">*</span></label>
                            <input type="text" name="telphone" class="form-control"  placeholder="Telepon" required value="<?php echo $detail->phone ?>" onkeypress="return isNumberKey(event)">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Email <span class="wajib">*</span></label>
                            <input type="email" name="email" class="form-control"  placeholder="Email" required value="<?php echo $detail->email ?>" disabled>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
<!--                             <label>Cabang <span class="wajib">*</span></label>
                            <input type="text" name="branch" class="form-control"  placeholder="Cabang" required value="<?php echo $detail->branch ?>"> -->
                            <label>Cabang <span class="wajib">*</span></label>
                            <select class="form-control select2"  required name="branch" id="branch">
                                <option value="">Pilih</option>
                                <?php 
                                    foreach($branch as $key=>$value ) { 
                                        $selected=$value->branch_code==$detail->branch_code?'selected':'';
                                        echo "<option value='{$value->branch_code}' {$selected} > {$value->description}</option>";
                                    }
                                ?>
                            </select>
                        </div> 

                        <div class="col-sm-6 form-group">
                            <label>Aktivasi <span class="wajib">*</span></label>
                            <select type="text" name="is_active" class="form-control select2"  required >
                                <option value="">Pilih</option>
                                <option value="<?php echo $this->enc->encode(1); ?>" <?php echo $detail->is_activation==1?"selected":"" ?> >YA</option>
                                <option value="<?php echo $this->enc->encode(0); ?>" <?php echo $detail->is_activation==0?"selected":"" ?> >TIDAK</option>
                            </select>
                        </div>
                        <div class="col-sm-12"></div> 

                        <div class="col-sm-6 form-group">
                            <label>Jabatan <span class="wajib">*</span></label>
                            <input type="text" name="position" class="form-control"  placeholder="Jabatan" value="<?php echo $detail->position ?>" required>
                        </div>


                        <div class="col-sm-12 ">
                            <div class="input-group">
                                <div class="icheck-inline">
                                    <?php 
                                        $detail->booking=='t'?$booking='checked':$booking='';
                                        $detail->redeem=='t'?$redeem='checked':$redeem='';

                                        $detail->topup_deposit=='t'?$topup_deposit='checked':$topup_deposit='';
                                        $detail->deposit=='t'?$deposit='checked':$deposit='';
                                        $detail->cash_out_deposit=='t'?$cash_out='checked':$cash_out='';

                                     ?>

                                    <input type="checkbox" class="allow" name='booking' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $booking ?> > Booking &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='redeem' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $redeem ?> > Reedem &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='topup_deposit' data-checkbox="icheckbox_flat-grey" value="1"  <?php echo $topup_deposit ?> > Topup Deposit &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='purchase_deposit' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $deposit ?> > purchase deposit &nbsp;&nbsp; 
                                    
                                    <input type="checkbox" class="allow" name='cash_Out' data-checkbox="icheckbox_flat-grey" value="1" <?php echo $cash_out ?> > Cash Out Deposit &nbsp;&nbsp;  



                                </div>
                            </div>
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

        rules   = {email: "required email"};
        messages= {email: "Format email tidak valid",nik: {minlength: jQuery.validator.format("Minimal {0} Karakter"), maxlength: jQuery.validator.format("Maximal {0} Karakter") }};

        validateForm('#ff',function(url,data){
            postData(url,data);
        });



        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#corporate_name").on("change",function(){

            var x=$("#corporate_name").val();
            $("[name='code_coporate']").val(x);
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });        


    })
</script>