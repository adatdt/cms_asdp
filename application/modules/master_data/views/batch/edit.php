<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title); ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/batch/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Kode Batch <span class="wajib">*</span></label>
                            <input type="text" name="batch_code" class="form-control"  placeholder="Kode Batch"
                            value="<?php echo $detail->batch_code; ?>" required disabled>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Batch <span class="wajib">*</span></label>
                            <input type="text" name="batch_name" class="form-control"  placeholder="Nama Batch"
                            value="<?php echo $detail->batch_name; ?>" required>
                            <input type="hidden" name="id"  value="<?php echo $id; ?>">
                            <input type="hidden" name="port"  value="<?= $this->enc->encode($detail->port_id); ?>">
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->port_id==$value->id?"selected":""; ?>> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ship_class">
                                <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->ship_class==$value->id?"selected":""; ?>> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div> -->
                </div>
            </div>
            <?php echo createBtnForm('Edit'); ?>
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