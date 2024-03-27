<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/dock/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">

                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select name="port" id="port" class="form-control select2" required> 
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->port_id?"selected":""; ?> ><?php echo strtoupper($value->name) ?></option>
                                <?php }?>
                            </select>

                            <input type="hidden" name="id">

                        </div>
                        <div class="col-sm-6">
                            <label>Nama Dermaga <span class="wajib">*</span></label>
                            <input type="text" name="dock_name" class="form-control" placeholder="Nama Dermaga" value="<?php echo $detail->name ?>" required >
                            <input type="hidden" name="dock" value="<?php echo $this->enc->encode($detail->id) ?>" required >
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Tarif Sandar<span class="wajib">*</span></label>
                            <input type="number" class="form-control" required name="dock_fee" id="dock_fee" placeholder="Nama Deramaga" onkeypress="return isNumberKey(event)" value="<?php echo $detail->fare ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tarif Tambat <span class="wajib">*</span></label>
                            <input type="number" class="form-control" required name="tambat_fee" id="tambat_fee" placeholder="Nama Deramaga" onkeypress="return isNumberKey(event)" value="<?php echo $detail->tambat_fare ?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
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