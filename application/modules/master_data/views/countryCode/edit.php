 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/countryCode/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama  <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama" required value="<?= $detail->name ?>">
                            <input type="hidden" name="id" required value="<?= $id ?>" >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Resmi<span class="wajib">*</span></label>
                            <input type="text" name="officialName" class="form-control"  placeholder="Nama Resmi" required value="<?= $detail->official_name ?>">
                        </div>

                        <!-- <div class="col-sm-12 form-group"></div> -->

<!--                         <div class="col-sm-6 form-group">
                            <label>continent alpha 2  <span class="wajib">*</span></label>
                            <input type="text" name="continentAlpha2" class="form-control"  placeholder="continent alpha 2" required value="<?= $detail->continent_alpha_2 ?>">
                        </div> -->

                        <div class="col-sm-6 form-group">
                            <label>Alpha 2 <span class="wajib">*</span></label>
                            <input type="text" name="alpha2" class="form-control"  placeholder="Alpha 2" required value="<?= $detail->alpha_2 ?>">
                        </div>


                        <div class="col-sm-6 form-group">
                            <label>Alpha 3 <span class="wajib">*</span></label>
                            <input type="text" name="alpha3" class="form-control"  placeholder="Alpha 3" required value="<?= $detail->alpha_3 ?>">
                        </div>
                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Nomor Negara <span class="wajib">*</span></label>
                            <input type="number" name="numericCountry" class="form-control"  placeholder="Nomor Negara" required min=1 value="<?= $detail->numeric_code ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Panggilan Negara <span class="wajib">*</span></label>
                            <input type="text" name="codeDial" class="form-control"  placeholder="Kode Panggil Negara" required  value="<?= $detail->dial_code ?>" onkeypress="return isNumberKey(event)" >
                        </div>                                                

                        <!-- <div class="col-sm-6 form-group">
                            <label>Kode Panggilan Negara </label>
                            <input type="number" name="codeDial" class="form-control"  placeholder="Kode Panggil Negara"  min=1 value="<?= $detail->dial_code ?>" >
                        </div>                                                                         -->

                        <div class="col-sm-12 form-group"></div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

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