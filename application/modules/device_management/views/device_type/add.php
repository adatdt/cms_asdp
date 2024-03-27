<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/device_type/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Nama Tipe Device <span class="wajib">*</span></label>
                            <input type="text" name="device_type" class="form-control" placeholder="Nama Tipe Device" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama Channel <span class="wajib">*</span></label>
                            <input type="text" name="channel" class="form-control" placeholder="channel" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Service <span class="wajib">*</span></label>
                            <select id="service" class="form-control  select2 " dir="" name="service" required>
                                <option value="">Pilih</option>
                                <option value="<?php echo $this->enc->encode(0); ?>">UMUM</option>
                                <?php foreach($service as $key=>$value ) {?>
                                <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
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