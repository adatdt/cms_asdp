
<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/dock/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select name="port" id="port" class="form-control select2" required> 
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php }?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Dermaga <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required name="dock" id="dock" placeholder="Nama Deramaga">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tarif <span class="wajib">*</span></label>
                            <input type="number" class="form-control" required name="dock_fee" id="dock_fee" placeholder="Nama Deramaga" onkeypress="return isNumberKey(event)">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tarif tambat <span class="wajib">*</span></label>
                            <input type="number" class="form-control" required name="tambat_fee" id="tambat_fee" placeholder="Nama Deramaga" onkeypress="return isNumberKey(event)">
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