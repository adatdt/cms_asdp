<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/sailing_company/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-6 form-group">
                            <label>Nama Pelayaran <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required name="segment" id="segment" placeholder="Nama Pelayaran" value="<?php echo $detail->segment ?>">

                            <input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Pelayaran <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required name="segment_code" id="segment_code" placeholder="Kode Pelayaran" value="<?php echo $detail->segment_code ?>">
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Perusaahaan Kapal<span class="wajib">*</span></label>
                            <select class="form-control select2" name="company" required >
                                <option value="">Pilih</option>
                                <?php foreach($company as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id)?>" <?php echo $value->id==$detail->company_id?"selected":""; ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>                                
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select name="port" id="port" class="form-control select2" required> 
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->port_id?"selected":""; ?> ><?php echo strtoupper($value->name) ?></option>
                                <?php }?>
                            </select>
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