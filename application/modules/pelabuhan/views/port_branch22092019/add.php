
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/ship_company/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="port">
                                <option value="">Pilih</option>
                                <?php  foreach ($port as $key => $value) { ?>
                                    <option value="<?php echo $this->enc->decode($value->id); ?>"> <?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Cabang<span class="wajib">*</span></label>
                            <input type="text" name="branch_name" class="form-control"  placeholder="Nama Cabang" required>
                        </div>

                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-6 form-group">
                            <label>Tipe <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ship_class">
                                <option value="">Pilih</option>
                                <?php  foreach ($ship_class as $key => $value) { ?>
                                    <option value="<?php echo $this->enc->decode($value->id); ?>"> <?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Cabang<span class="wajib">*</span></label>
                            <input type="text" name="branch_code" class="form-control"  placeholder="Kode Cabang" required>
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