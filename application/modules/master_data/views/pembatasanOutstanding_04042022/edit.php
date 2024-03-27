 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pembatasanOutstanding/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Tanggal Mulai<span class="wajib">*</span></label> 
                            <input type="text" name="startDate" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly  value="<?= $detail->start_date ?>" >   
                            <input type="hidden" name='id' id="id" value="<?= $id ?>">                    
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text" name="endDate" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly value="<?= $detail->end_date ?>"  >                       
                        </div>
                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-6 form-group">
                            <label>Nominal Pembatasan Transaksi<span class="wajib">*</span></label> 
                            <input type="number" name="value" class="form-control " min=1 required placeholder="Pembatasan Transaksi"  value="<?= $detail->value ?>"  >                     
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Layanan<span class="wajib">*</span></label>   
                            <?= form_dropdown("shipClass",$shipClass,$selectedShipClass,' class="form-control select2" required id="shipClass" disabled ') ?>                 
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

        $('.waktu').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "<?php echo date('Y-m-d H:i',strtotime('-5 minutes')) ?>",
        });        
    })
</script>