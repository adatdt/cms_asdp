 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{
        color:red;
    }

</style>

<div class="col-md-12 col-md-offset-0">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">

            <?php echo form_open('fare/discount/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row" id="form">

                        <div class="col-sm-3 form-group">
                            <label>Schema Discount<span class="wajib">*</span></label>
                            <select class="form-control select2" name="discount_schema" id="discount_schema" required >
                                <option value="null">Pilih</option>
                                <?php foreach($discount_schema as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode("$value->schema_code")?>" ><?php echo strtoupper($value->description) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Kode Schema <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="schema_code" id="schema_code" required placeholder="Kode Schema" readonly>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Awal Berlaku <span class="wajib">*</span></label>
                            <input type="text" class="form-control date" name="start_date" id="start_date" required placeholder="YYYY-MM-DD HH:II">

                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir Berlaku<span class="wajib">*</span></label>
                            <input type="text" class="form-control date" name="end_date" id="end_date" required placeholder="YYYY-MM-DD HH:II">

                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-3 form-group">
                            <label>Nama Promo <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="description" id="description" required placeholder="Nama Promo">

                        </div>


                        <div class='col-sm-3 form-group'>                                    
                            <label>Jam Awal Berlaku<span class='wajib'>*</span></label>
                            <input type='time' class='form-control' name='start_time' id='start_time' required  step="2"  >
                        </div>

                       <div class='col-sm-3 form-group'>                                    
                            <label>Jam Akhir Berlaku<span class='wajib'>*</span></label>
                            <input type='time' class='form-control' name='end_time' id='end_time' required  step="2" >
                        </div>


                        <div class="col-sm-12 " id="get_form"><hr></div>

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
        var rules = {start_time: {pattern: '[0-9,]{2}:[0-9]{2}:[0-9]{2}'},end_time: {pattern: '[0-9,]{2}:[0-9]{2}:[0-9]{2}'} }

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('.date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });


    })
</script>
<?php include "fileJs.php"; ?>