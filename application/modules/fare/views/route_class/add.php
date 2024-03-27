 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color:red;}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/route_class/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Rute <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="rute" id="rute">
                                <option value="">Pilih</option>
                                <?php foreach($route as $key=>$value ) { ?>
                                <option value="<?php echo $value->id ?>"><?php echo strtoupper($value->route_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Rute <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ship_class" id="ship_class">
                                    <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6 form-group">
                            <label>IFCS <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ifcs" id="ifcs">
                                <option value="">Pilih</option>
                                <option value="<?php echo $this->enc->encode(1) ?>">IYA</option>
                                <option value="<?php echo $this->enc->encode(2) ?>">TIDAK</option>
                            </select>
                        </div>                        

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
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
    })
</script>