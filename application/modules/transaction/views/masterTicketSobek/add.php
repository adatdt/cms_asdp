
<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/masterTicketSobek/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-6 form-group">
                            <label>No Tiket <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required name="ticket_number" id="dock" placeholder="Nomer Tiket" >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan<span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,"", 'class="form-control select2" required  id="port" ' ) ?>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Jenis PJ <span class="wajib">*</span></label>
                            <?= form_dropdown("service",$service,"", 'class="form-control select2" required  id="service" ' ) ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Layanan <span class="wajib">*</span></label>
                            <?= form_dropdown("layanan",$layanan,"", 'class="form-control select2" required  id="layanan" ' ) ?>
                        </div>                                                            

                        <div class="col-sm-12 form-group" ></div>
                        <div class="col-sm-6 form-group" id="golongan"></div>


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

        $("#service").on("change",function(){
            
            var getValue=$("#service option:selected").html();

            getValue=getValue.replace(" ","_");

            html =``
            if(getValue.toLowerCase()=='kendaraan')
            {
                html +=`<label>Golongan Kendaraan <span class="wajib">*</span></label>
                        <?= form_dropdown("golongan",$vehicleClass,"", 'class="form-control select2" required  id="golongan" ' ) ?>`
                
            }
            else if(getValue.toLowerCase()=='pejalan_kaki')
            {
                html +=`<label>Golongan Pejalan Kaki <span class="wajib">*</span></label>
                        <?= form_dropdown("golongan",$passangerType,"", 'class="form-control select2" required  id="golongan" ' ) ?>`   
            }
            else
            {
                htm +=``;
            }

            $("#golongan").html(html);

            $('.select2:not(.normal)').each(function () {
                $(this).select2({
                    dropdownParent: $(this).parent()
                });
            });

        })
    })
</script>