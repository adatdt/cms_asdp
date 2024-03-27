 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color:red;}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/route/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan Asal <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="origin" id="origin">
                                <?php if(!empty($this->session->userdata('port_id'))) { ?>
                                <?php } else { ?>
                                    <option value="">Pilih</option>
                                <?php } ?>
                                <?php foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->origin?"selected":""; ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>

                            <input type="hidden" name="id" value="<?php echo $this->enc->encode($detail->id); ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan Tujuan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="destination" id="destination">
                                    <option value="">Pilih</option>
                                <?php foreach($destination as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->destination?"selected":""; ?>><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
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