
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/corporate/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Corporate <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama Corporate" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Corporate <span class="wajib">*</span></label>
                            <input type="text" name="code" class="form-control"  placeholder="Kode Corporate" value='<?php echo $corporate_code ?>' required>
                        </div>

<!--                         <div class="col-sm-1 form-group">
                            <label>&nbsp;</label>
                            <a class="btn btn-warning" id="generate">Buat Kode</a>
                            
                        </div>
 -->
                        <div class="col-sm-12 "></div>                        

                        <div class="col-sm-6 form-group">
                            <label>Bidang Perusahaan <span class="wajib">*</span></label>
                            <?php echo form_dropdown("sector",$sector_company,'',' class="form-control select2"  placeholder="Sector" required') ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Telpon<span class="wajib">*</span></label>
                            <input type="text" name="telphone" class="form-control"  placeholder="Telpon" required onkeypress="return isNumberKey(event)">
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-6 form-group">
                            <label>Email<span class="wajib">*</span></label>
                            <input type="email" name="email" class="form-control"  placeholder="email" required>
                        </div>

                        
                        <div class="col-sm-6 form-group">
                            <label>Alamat<span class="wajib">*</span></label>
                            <input type="text" name="address" class="form-control"  placeholder="address" required>
                        </div> 

<!--                         <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-sm-12 form-group"><a href="#" class="btn btn-warning pull-left" id="tambah"><i class="fa fa-plus"></i> Tambah Cabang</a> </div>

                        <div id="branch" class="col-sm-12 form-group"></div>       -->                 


                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>

<?php include "fileJs.php" ?>

<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
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