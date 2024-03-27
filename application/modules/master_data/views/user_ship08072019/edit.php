 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/user_ship/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Username<span class="wajib">*</span></label>
                            <select name="username" class="form-control select2" required>
                                <option value=''>Pilih</option>
                                <?php foreach($username as $key=>$value){ ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->user_id==$value->id?"selected":"" ?> ><?php echo $value->username ?></option>
                                <?php }  ?>
                            </select>

                            <input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Perusahaan <span class="wajib">*</span></label>
                            <select  name="company" class="form-control select2"  required>
                                <option value="">Pilih</option>
                                <?php foreach($company as $key=>$value){ ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->company_id==$value->id?"selected":"" ?> ><?php echo strtoupper($value->name) ?></option>
                                <?php }  ?>
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