
<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/sailing_company/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                    
                        <div class="col-sm-6 form-group">
                            <label>Nama Pelayaran <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required name="segment" id="segment" placeholder="Nama Pelayaran">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Pelayaran <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required name="segment_code" id="segment_code" placeholder="Kode Pelayaran">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Perusaahaan Kapal<span class="wajib">*</span></label>
                            <select class="form-control select2" name="company" required >
                                <option value="">Pilih</option>
                                <?php foreach($company as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id)?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>                                
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select name="port" id="port" class="form-control select2" required> 
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php }?>
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