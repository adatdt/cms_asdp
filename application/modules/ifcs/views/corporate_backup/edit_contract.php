
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/corporate/action_edit_contract', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Kode Corporate<span class="wajib">*</span></label>
                            <input type="text" name="corporate_code" class="form-control" value="<?php echo $detail->corporate_code ?>"  placeholder="Kode Corporate" required readonly>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Corporate <span class="wajib">*</span></label>
                            <input type="text" name="corporate" class="form-control" value="<?php echo $data_corporate->corporate_name ?>" placeholder="Nama Corporate" required readonly>
                        </div>
                        <div class="col-sm-12 "></div>
                        <div class="col-sm-6 form-group">
                            <label>Awal Kontrak <span class="wajib">*</span></label>
                            <input type="text" name="start_date" class="form-control date"  placeholder="YYYY-MM-DD" required value="<?php echo $detail->start_date ?>">
                            <input type="hidden" name="id" required value="<?php echo $this->enc->encode($detail->id) ?>">
                            <input type="hidden" name="last_reward_code" required value="<?php echo $detail->last_reward_code ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Akhir Kontrak <span class="wajib">*</span></label>
                            <input type="text" name="end_date" class="form-control date"  placeholder="YYYY-MM-DD" required value="<?php echo $detail->end_date ?>">
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

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });        
    })
</script>