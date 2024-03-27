
<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                       
                    <div class="portlet-body">

                        <div class="row">
                            <div class="col-sm-6 ">

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Nomer Tiket</div>
                                        <input type="text" name="search" class="form-control" placeholder="Nomer Tiket" id="search">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" id="cari"> cari</button>  
                                    </span>
                                </div>  
                                <font style="font-size:12px; color:red; font-style: italic ">Jika tiket kendaraan, harus diinpukan nomer tiket kendaraanya *</font>

                                
                            </div>

                            <div class="col-sm-3 "></div>

                            <div class="col-sm-3 pull-right">

                                Status : <span class="label label-success">Statusnya</span>


                            </div>

                        </div>

                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>
            <p></p>
            <?php echo form_open('master_data/user_bank/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Nomer Tiket<span class="wajib"> *</span></label>
                            <input type="text" name="ticket_number" class="form-control" placeholder="Nomer Tiket" readonly required>

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Kode Booking<span class="wajib"> *</span></label>
                            <input type="text" name="booking_code" class="form-control" placeholder="Kode Booking" readonly required>

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama<span class="wajib"> *</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" readonly required>

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Jenis Kelamin<span class="wajib"> *</span></label>
                            <input type="text" name="gender" class="form-control" placeholder="Jenis Kelamin" readonly >

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Servis<span class="wajib"> *</span></label>
                            <input type="text" name="service" class="form-control" placeholder="Servis" readonly >
                        </div>

                        <div class="col-sm-4 form-group">
                            <label id="label"> Tipe Penumpang</label>
                            <input type="text" name="passanger_type" class="form-control" placeholder="Tipe Penumpang" readonly >
                        </div>

                        <div class="col-sm-4 form-group" id="plat">
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

<?php include "fileJs.php"; ?>