
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/user_corporate/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Corporate <span class="wajib">*</span></label>
                            <select name="corporate_name" id="corporate_name" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php foreach($corporate as $key=>$value ) { ?>
                                    <option value="<?php echo $value->corporate_code ?>"><?php echo strtoupper($value->corporate_name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Corporate <span class="wajib">*</span></label>
                            <input type="text" name="code_coporate" class="form-control"  placeholder="Kode Coporate" required readonly>
                        </div> 

                        <div class="col-sm-12 form-group"></div>                       

                        <div class="col-sm-6 form-group">
                            <label>Tipe User <span class="wajib">*</span></label>
                            <select name="member_type" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php foreach($member_type as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>                                
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama" required>
                        </div>

                        <div class="col-sm-12 form-group"></div>                        

                        <div class="col-sm-6 form-group">
                            <label>No. KTP <span class="wajib">*</span></label>
                            <input type="text" name="nik" class="form-control"  placeholder="No. KTP" required onkeypress="return isNumberKey(event)" minlength="16" maxlength="16">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>No. Induk Kepegawaian <span class="wajib">*</span></label>
                            <input type="text" name="nip" class="form-control"  placeholder="No. Induk Kepegawaian" required>
                        </div>                        

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Telepon<span class="wajib">*</span></label>
                            <input type="text" name="telphone" class="form-control"  placeholder="Telepon" onkeypress="return isNumberKey(event)" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Email <span class="wajib">*</span></label>
                            <input type="email" name="email" class="form-control"  placeholder="Email" required>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
<!--                             <label>Cabang <span class="wajib">*</span></label>
                            <input type="text" name="branch" class="form-control"  placeholder="Cabang" required> -->
                            <label>Cabang <span class="wajib">*</span></label>
                            <select class="form-control select2"  required name="branch" id="branch">
                                <option value="">Pilih</option>
                            </select>
                        </div> 

                        <div class="col-sm-6 form-group">
                            <label>Password <span class="wajib">*</span></label>
                            <input type="password" name="password" class="form-control"  placeholder="Password" required>
                        </div>

                        <div class="col-sm-12"></div> 

                        <div class="col-sm-6 form-group">
                            <label>Jabatan <span class="wajib">*</span></label>
                            <input type="text" name="position" class="form-control"  placeholder="Jabatan" required>
                        </div>
                        
                        <div class="col-sm-12 form-group"></div> 

                        <div class="col-sm-12 form-group ">
                            <div class="input-group">
                                <div class="icheck-inline">

                                    <input type="checkbox" class="allow" name='booking' data-checkbox="icheckbox_flat-grey" value="1" > Booking &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='redeem' data-checkbox="icheckbox_flat-grey" value="1" > Reedem &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='topup_deposit' data-checkbox="icheckbox_flat-grey" value="1" > Topup Deposit &nbsp;&nbsp; 

                                    <input type="checkbox" class="allow" name='purchase_deposit' data-checkbox="icheckbox_flat-grey" value="1" > purchase deposit &nbsp;&nbsp; 
                                    
                                    <input type="checkbox" class="allow" name='cash_Out' data-checkbox="icheckbox_flat-grey" value="1" > Cash Out Deposit &nbsp;&nbsp; 


                                </div>
                            </div>
                        </div>                   


                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<?php include "fileJs.php"; ?>
<script type="text/javascript">

    function get_branch()
    {        
        $.ajax({
            type:"post",
            url:"<?php echo site_url()?>ifcs/user_corporate/get_branch",
            data: 'corporate_code='+$("#corporate_name").val(),
            dataType :"json",
            beforeSend:function(){
                unBlockUiId('box')
            },
            success:function(x){

                var html="<option value=''>Pilih</option>";
                        // <option value='all'>Pusat</option>";

                for(var i=0; i<x.length; i++)
                {
                    html +="<option value='"+x[i].branch_code+"'>"+x[i].description+"</option>";                   
                }

                $("#branch").html(html);

                console.log(x)
            },

            complete: function(){
                $('#box').unblock(); 
            }

        });
    }
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

            get_branch();
            var x=$("#corporate_name").val();
            $("[name='code_coporate']").val(x);
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });        


    })
</script>