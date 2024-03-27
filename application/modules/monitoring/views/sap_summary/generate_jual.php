<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('monitoring/sap_summary/summary_terjual', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <!-- <div class="col-sm-6 form-group">
                            <label>Kode Laporan<span class="wajib">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder='Kode Laporan' required readonly value="<?php echo $detail->report_code ?>">
                        </div> -->

                        <div class="col-sm-12 form-group">
														<h4>Apakah Anda yakin men-generate data ini ?</h4>
                            <!-- <label>Nama<span class="wajib">*</span></label> -->
                            <input type="hidden" name="shift_date" class="form-control" value="<?php echo $detail->shift_date ?>">
														<input type="hidden" name="port_id" class="form-control" value="<?php echo $detail->port_id ?>">
														<input type="hidden" name="shift_id" class="form-control" value="<?php echo $detail->shift_id ?>">
                            <input type="hidden" name="ship_class" class="form-control" value="<?php echo $detail->ship_class ?>">
                            <input type="hidden" name="type" class="form-control" value="<?php echo $detail->type ?>">
														<input type="hidden" name="id" class="form-control" value="<?php echo $detail->id ?>">
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Generate') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

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