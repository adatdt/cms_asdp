<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/masterTicketSobek/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>No Tiket <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required name="ticket_number" id="dock" placeholder="Nomer Tiket" value="<?= $getData->ticket_number ?>">

                            <input type="hidden" class="form-control" required name="id" id="id" placeholder="Nomer Tiket" value="<?= $id ?>">
                            <input type="hidden" class="form-control" required name="serviceId" id="serviceId"  value="<?= $selectedService ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan<span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,$selectedPort, 'class="form-control select2" required  id="port" ' ) ?>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Jenis PJ <span class="wajib">*</span></label>
                            <?= form_dropdown("service",$service,$selectedService, 'class="form-control select2" required  id="service" disabled' ) ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Layanan <span class="wajib">*</span></label>
                            <?= form_dropdown("layanan",$layanan,$selectedLayanan, 'class="form-control select2" required  id="layanan" ' ) ?>
                        </div>                                                            

                        <div class="col-sm-12 form-group" ></div>
                        <div class="col-sm-6 form-group" id="golongan">
                            
                            <label>Golongan <?= $titleGolongan; ?> <span class="wajib">*</span></label>
                            <?= form_dropdown("golongan",$golongan,$selectedGolongan, 'class="form-control select2" required  id="golongan" ' ) ?>

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