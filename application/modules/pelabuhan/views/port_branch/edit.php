 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/port_branch/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">


                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="port">
                                <?php if(!empty($this->session->userdata("port_id"))) {?>
                                <?php } else { ?>
                                <option value="">Pilih</option>
                                <?php } foreach ($port as $key => $value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->port_id?"selected":""; ?> > <?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Cabang<span class="wajib">*</span></label>
                            <input type="text" name="branch_name" class="form-control"  placeholder="Nama Cabang"  value="<?php echo $detail->branch_name ?>" required>
                        </div>

                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-6 form-group">
                            <label>Tipe <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ship_class">
                                <option value="">Pilih</option>
                                <?php  foreach ($ship_class as $key => $value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->ship_class?"selected":""; ?> > <?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Cabang<span class="wajib">*</span></label>
                            <input type="text" name="branch_code" class="form-control"  placeholder="Kode Cabang" required value="<?php echo $detail->branch_code ?>">

                            <input type="hidden" value="<?php echo $id ?>" name="id" >
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