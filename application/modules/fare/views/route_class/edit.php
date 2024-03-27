 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color:red;}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/route_class/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Rute <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="rute" id="rute">
                                <option value="">Pilih</option>
                                <?php foreach($route as $key=>$value ) { ?>
                                <option value="<?php echo $value->id ?>" <?php echo $this->enc->decode($value->id)==$detail->rute_id?"selected":"" ?> ><?php echo strtoupper($value->route_name); ?></option>
                                <?php } ?>
                            </select>

                            <input type="hidden" name="id" value="<?php echo $this->enc->encode($detail->id)?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Rute <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ship_class" id="ship_class">
                                    <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->ship_class?"selected":"" ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>IFCS <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ifcs" id="ifcs">
                                <option value="">Pilih</option>
                                <option value="<?php echo $this->enc->encode(1) ?>" <?php echo $detail->ifcs=='t'?'selected':''; ?> >IYA</option>
                                <option value="<?php echo $this->enc->encode(2) ?>" <?php echo $detail->ifcs=='f'?'selected':''; ?> >TIDAK</option>
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