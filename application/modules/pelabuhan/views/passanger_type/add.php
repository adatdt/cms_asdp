
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/passanger_type/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control"  placeholder="Nama Tipe Pejalan Kaki" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Max Umur<span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="maxAge" placeholder="Max Umur" required onkeypress="return isNumberKey(event)" >
                        </div>

                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-6 form-group">
                            <label>Min Umur<span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="minAge" placeholder="Min Umur" required onkeypress="return isNumberKey(event)" >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Urutan<span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="ordering" placeholder="Urutan" required onkeypress="return isNumberKey(event)" >
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Tipe Penumpang<span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="code" placeholder="Kode" maxlength=10>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>keterangan<span class="wajib">*</span></label>
                            <textarea class="form-control" name="description" placeholder="Keterangan" required></textarea>
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
<script type="text/javascript">
    $(document).ready(function(){

        rules =  {
            code : { maxlength: 10, required: true}
        }

        messages= {
            code : { 
                maxlength: jQuery.validator.format("Maximal {0} Karater")
            }
        }

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