 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/corporate/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Kode Corporate <span class="wajib">*</span></label>
                            <input type="text" name="code" class="form-control"  placeholder="Kode Corporate" disabled required value="<?php echo $detail->corporate_code ?>">

                            <input type="hidden" name="corporate" required value="<?php echo $id ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Bidang Perusahaan <span class="wajib">*</span></label>
                            <?php echo form_dropdown("sector",$sector_company,$detail->business_sector_code,' class="form-control select2"  placeholder="Sector" required') ?>
                        </div>                        

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Corporate <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama Corporate" required value="<?php echo $detail->corporate_name ?>" >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Telpon Corporate<span class="wajib">*</span></label>
                            <input type="text" name="telphone" class="form-control"  placeholder="Telpon" required value="<?php echo $detail->phone ?>" onkeypress="return isNumberKey(event)">
                        </div>

                        <div class="col-sm-12 form-group"></div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Email Corporate<span class="wajib">*</span></label>
                            <input type="email" name="email" class="form-control"  placeholder="email" required value="<?php echo $detail->email ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Alamat Corporate<span class="wajib">*</span></label>
                            <input type="text" name="address" class="form-control"  placeholder="address" required value="<?php echo $detail->corporate_address ?>">
                        </div>
                        <div class="col-sm-12"></div>

                        <div class="col-sm-6 form-group">
                            <label>PIC<span class="wajib">*</span></label>
                            <input type="text" name="pic" class="form-control"  placeholder="PIC" value="<?php echo $detail->pic_name ?>"  required>
                        </div> 

                        <div class="col-sm-6 form-group">
                            <label>Jabatan<span class="wajib">*</span></label>
                            <input type="text" name="position" class="form-control"  placeholder="Jabatan" required value="<?php echo $detail->pic_position ?>">
                        </div> 

                        <div class="col-sm-12"></div>

                        <div class="col-sm-6 form-group">
                            <label>Email PIC<span class="wajib">*</span></label>
                            <input type="email" name="pic_email" class="form-control"  placeholder="Email PIC" required value="<?php echo $detail->pic_email ?>">
                        </div>                         

                        <div class="col-sm-6 form-group">
                            <label>Nomer Telpon PIC<span class="wajib">*</span></label>
                            <input type="text" name="pic_phone" class="form-control"  placeholder="Nomer Telpon PIC" onkeypress="return isNumberKey(event)" required value="<?php echo $detail->pic_phone ?>">
                        </div>                                                 

<!--                         <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-sm-12 form-group"><a href="#" class="btn btn-warning pull-left" id="tambah"><i class="fa fa-plus"></i> Tambah Cabang</a> </div>

                        <div class="col-sm-12 form-group"><?php echo $detail_branch ?></div> 
                        <div class="col-sm-12 form-group" id="branch"></div>                  -->                                 


                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- <?php include "fileJs.php" ?> -->

<script type="text/javascript">


    $(document).ready(function(){

        rules   = {email: "required email"};
        messages= {email: "Format email tidak valid"};

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>