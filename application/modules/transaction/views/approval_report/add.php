
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/approval_report/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Laporan<span class="wajib">*</span></label>
                            <select name="report_name" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php foreach($master_report as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->report_code) ?>">
                                        <?php echo strtoupper($value->report_name) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Laporan Tanggal<span class="wajib">*</span></label>

                                <input type="text" name="date"  class="form-control date" id="date"  placeholder="YYYY-MM-DD"  required>
                                <!-- <div class="input-group-addon"><i class="icon-calendar"></i></div> -->

                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Shift<span class="wajib">*</span></label>
                            <select name="shift" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>">
                                        <?php echo strtoupper($value->shift_name) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan<span class="wajib">*</span></label>
                            <select name="port" class="form-control select2" required>
                                <?php if(empty($port_id)) { ?>
                                <option value="">Pilih</option>
                                <?php } foreach($port as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>">
                                        <?php echo strtoupper($value->name) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe<span class="wajib">*</span></label>
                            <select name="ship_class" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php  foreach($ship_class as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>">
                                        <?php echo strtoupper($value->name) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Approve'); ?>
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

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        });
    })
</script>