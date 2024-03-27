
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/countryCode/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama  <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Resmi<span class="wajib">*</span></label>
                            <input type="text" name="officialName" class="form-control"  placeholder="Nama Resmi" required>
                        </div>


<!--                         <div class="col-sm-6 form-group">
                            <label>continent alpha 2  <span class="wajib">*</span></label>
                            <input type="text" name="continentAlpha2" class="form-control"  placeholder="continent alpha 2" required>
                        </div> -->
                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Alpha 2 <span class="wajib">*</span></label>
                            <input type="text" name="alpha2" class="form-control"  placeholder="Alpha 2" required>
                        </div>


                        <div class="col-sm-6 form-group">
                            <label>Alpha 3 <span class="wajib">*</span></label>
                            <input type="text" name="alpha3" class="form-control"  placeholder="Alpha 3" required>
                        </div>
                        <div class="col-sm-12 form-group"></div>


                        <div class="col-sm-6 form-group">
                            <label>Nomor Negara <span class="wajib">*</span></label>
                            <input type="number" name="numericCountry" class="form-control"  placeholder="Nomor Negara" required min=1 >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Panggilan Negara <span class="wajib">*</span></label>
                            <input type="text" name="codeDial" class="form-control"  placeholder="Kode Panggil Negara" required   onkeypress="return isNumberKey(event)">
                        </div>
                        <!-- <div class="col-sm-6 form-group">
                            <label>Kode Panggilan Negara </label>
                            <input type="number" name="codeDial" class="form-control"  placeholder="Kode Panggil Negara"  min=1 >
                        </div>-->

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode != 45  && charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }    
    $(document).ready(function(){
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