
<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                       
                    <div class="portlet-body">

                        <div class="row">
                                
                            <div class="col-lg-6 col-md-4 col-xs-12">
                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-color-primary uppercase">Kode Force Majeure</div>
                                    <p class="ribbon-content" id="total"><?php echo $force_code; ?></p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-4 col-xs-12">
                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-color-primary uppercase">Waktu Perpanjangan Expired</div>
                                    <p class="ribbon-content" id="total"><?php echo $extend_param; ?> Jam</p>
                                </div>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-sm-6 ">

                                 <!--  <?php echo date('Y-m-d',strtotime('2019-03-12 00:00:00')) ?> -->

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Nomer Tiket :</div>
                                        <input type="text" name="search" class="form-control" placeholder="Nomer Tiket" id="search">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" id="cari"> cari</button>  
                                    </span>
                                </div>  
                                <font style="font-size:12px; color:red; font-style: italic ">Jika tiket kendaraan, harus diinpukan nomer tiket kendaraanya *</font>

                                
                            </div>

                            <div class="col-sm-6 ">
<!--                                 <div class="pull-right btn-add-padding " style="padding-left: 5px">
                                    <button class="btn btn-warning btn-sm pull-right" type="buton" name="tampung">Tampung</button>
                                </div> -->

                                <div class="pull-right btn-add-padding" style="padding-left: 5px">
                                    <button class="btn btn-primary btn-sm pull-right" type="buton" name="clear">Bersihkan</button>
                                </div>

                            </div>

                            <div class="col-sm-12 "></div>
                            <div class="col-sm-12 "></div>
                            <div class="col-sm-12 "></div>
                            <div class="col-sm-3 " id="status"></div>

                        </div>

                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>
            <p></p>
            <?php echo form_open('transaction/force_majeure/action_add_force_majeure_eks', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Nomer Tiket<span class="wajib"> *</span></label>
                            <input type="text" name="ticket_number" class="form-control" placeholder="Nomer Tiket" readonly >

                            <input type="hidden" name="fcode" value="<?php echo $force_code; ?>" required readonly>
                            <input type="hidden" name="extend_param" value="<?php echo $extend_param; ?>" required >
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Kode Booking<span class="wajib"> *</span></label>
                            <input type="text" name="booking_code" class="form-control" placeholder="Kode Booking" readonly >

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama<span class="wajib"> *</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" readonly >

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Servis<span class="wajib"> *</span></label>
                            <input type="text" name="service" class="form-control" placeholder="Servis" readonly >
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Jenis Kelamin</label>
                            <input type="text" name="gender" class="form-control" placeholder="Jenis Kelamin" readonly >

                        </div>

                        <div class="col-sm-4 form-group">
                            <label id="label"> Tipe Penumpang</label>
                            <input type="text" name="passanger_type" class="form-control" placeholder="Tipe Penumpang" readonly >
                        </div>
                        <div class="col-sm-12 form-group" ></div>
                        <div class="col-sm-4 form-group" id="plat">
                        </div>

                        <div class="col-sm-12 form-group"><a class="btn btn-danger btn-sm" href="#" id="hapus">Hapus Tampung</a></div>
                        <div class="col-sm-12 form-group">
                            <table class="table table-bordered table-striped   table-hover" id="data_temp">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NO</th>
                                        <th>NOMER TIKET</th>
                                        <th>KODE BOOKING</th>
                                        <th>NAMA PENUMPANG/ DRIVER</th>
                                        <th>SERVIS</th>
                                    </tr>
                                </thead>
                                <tbody id="table_tampung">
                                    
                                </tbody>
                            </table>
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

    function sendData(url, data)
    {
        $.ajax({
            url         : url,
            data        : data,
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                if(json.code == 1){
                    // unblockID('#form_edit');
                    closeModal();
                    toastr.success(json.message, 'Sukses');
                    $('#dataTables').DataTable().ajax.reload( null, false );
                    $('#dataTables2').DataTable().ajax.reload( null, false );
                }
                else
                {
                    toastr.error(json.message, 'Gagal');
                }
            },

            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            },

            complete: function(){
                $('#box').unblock(); 
            }
        });
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            sendData(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>

<?php include "fileJs.php"; ?>