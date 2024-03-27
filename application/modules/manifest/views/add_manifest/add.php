 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{
        color:red;
    }
</style>

<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <div class="box-body">
                
                <div class="row">
                    
                    <div class="col-md-12 col-sm-12 col-xl-12">
                        
                        <div class="table-toolbar">
                            <div class="row">

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Kode Boarding</div>
                                        <p class="ribbon-content"><?php echo $boarding_code; ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Nama Kapal</div>
                                        <p class="ribbon-content"><?php echo $data_header->ship_name; ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Tanggal Boarding</div>
                                        <p class="ribbon-content"><?php echo empty($data_header->created_on)?"":format_dateTimeHis($data_header->created_on); ?></p>

                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Shift</div>
                                        <p class="ribbon-content"><?php echo $data_header->shift_name ?></p>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-4 col-xs-12"></div>
                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Tipe Kapal</div>
                                        <p class="ribbon-content"><?php echo $data_header->ship_class_name ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-4 col-xs-12"></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-12 col-sm-12 col-xl-12 ">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>Servis <span class="wajib">*</span></label>

                                        <select class="form-control select2" id="service2" name="service2">
                                            <option value="">Pilih</option>
                                            <?php foreach ($service as $key=>$value ) {?>
                                                <option value="<?php echo $this->enc->encode($value->id)?>">
                                                    <?php echo strtoupper($value->name); ?>
                                                </option>
                                            <?php } ?>
                                        </select>


                                </div>

                                <div class="col-md-4 form-group">
                                    <label>Nomor tiket <span class="wajib">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="ticket_number2" id="ticket_number2" class="form-control"  placeholder="No Ticket">
                                        <span class="input-group-btn">
                                            <button class="btn default green-meadow" type="button" id="btnTicketNumber">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12 form-group"></div>

                            </div>
                        </div>
                    </div>                    
                </div>



            </div>
        </div>
        
        <div class="portlet-body">
            <?php echo form_open('manifest/add_manifest/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xl-12" id='result'></div>

                        <input type="hidden" name="ticket_number" id="ticket_number">
                        <input type="hidden" name="service" id="service">
                        <input type="hidden" required  name="boarding_code" id="boarding_code">
                        <input type="hidden" required  name="ship_class_boarding" id="ship_class_boarding">

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

    function getData()
    {
        var service=$("#service2").val();
        var ticket_number=encodeURIComponent($("#ticket_number2").val());

        console.log(ticket_number);

        $.ajax({
            data:"service="+service+"&ticket_number="+ticket_number,
            dataType:"json",
            type:"post",
            url:"<?php echo site_url()?>manifest/add_manifest/get_data",
            beforeSend:function(){
                unBlockUiId('box')
            },
            success:function(x)
            {
                var html ="";

                if(x.tipe_penumpang=='penumpang')
                {

                    html+=  '<div class="table-scrollable"><table class="table table-striped table-bordered table-hover">'+
                               '<tr><td colspan="10">Data Ticket Penumpang</td></tr>'+
                               '<tr>'+
                                    '<td align="center">NOMER IDENTITAS</td>'+
                                    '<td align="center">NAMA</td>'+
                                    '<td align="center">TANGGAL LAHIR</td>'+
                                    '<td align="center">JENIS KELAMIN</td>'+
                                    '<td align="center">USIA</td>'+
                                    '<td align="center">NOMER TICKET</td>'+
                                    '<td align="center">NOMER BOOKING</td>'+
                                    '<td align="center">JENIS PENUMPANG</td>'+
                                    '<td align="center">SERVIS</td>'+
                                    '<td align="center">TIPE</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td>'+x.id_number+'</td>'+
                                    '<td>'+x.name+'</td>'+
                                    '<td>'+x.birth_date+'</td>'+
                                    '<td>'+x.gender+'</td>'+
                                    '<td>'+x.age+'</td>'+
                                    '<td>'+x.ticket_number+'</td>'+
                                    '<td>'+x.booking_code+'</td>'+
                                    '<td>'+x.passanger_type+'</td>'+
                                    '<td>'+x.service_name+'</td>'+
                                    '<td>'+x.ship_class_name+'</td>'+

                                '</tr>'+
                            '</table></div>'

                            $("#result").html(html);
                            $("#boarding_code").val("<?php echo $boarding_code ?>");
                            $("#ship_class_boarding").val("<?php echo $ship_class ?>");


                            $("#service").val(service);
                            $("#ticket_number").val(x.ticket);

                }

                else if(x.tipe_penumpang=='kendaraan')
                {

                    html+=  '<div class="table-scrollable"><table class="table table-striped table-bordered table-hover">'+
                               '<tr><td colspan="12">Data Ticket Kendaraan</td></tr>'+
                               '<tr>'+
                                    '<td align="center">NOMER TICKET</td>'+
                                    '<td align="center">NOMER BOOKING</td>'+
                                    '<td align="center">NOMER IDENTITAS</td>'+
                                    '<td align="center">NAMA <br> PENGEMUDI</td>'+
                                    '<td align="center">TANGGAL LAHIR</td>'+
                                    '<td align="center">JENIS KELAMIN</td>'+
                                    '<td align="center">USIA</td>'+
                                    '<td align="center">SERVIS</td>'+
                                    '<td align="center">GOLONGAN KENDARAAN</td>'+
                                    '<td align="center">NOMER PLAT</td>'+
                                    '<td align="center">TOTAL PENUMPANG</td>'+
                                    '<td align="center">TIPE</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td>'+x.ticket_vehicle+'</td>'+
                                    '<td>'+x.booking_code+'</td>'+
                                    '<td>'+x.id_number+'</td>'+
                                    '<td>'+x.name+'</td>'+
                                    '<td>'+x.birth_date+'</td>'+
                                    '<td>'+x.gender+'</td>'+
                                    '<td>'+x.age+'</td>'+
                                    '<td>'+x.tipe_penumpang+'</td>'+
                                    '<td>'+x.vehicle_class_name+'</td>'+
                                    '<td>'+x.plate_number+'</td>'+
                                    '<td>'+x.total_passanger+'</td>'+
                                    '<td>'+x.ship_class_name+'</td>'+
                                '</tr>'+
                            '</table> </div>'

                            $("#result").html(html);
                            $("#boarding_code").val("<?php echo $boarding_code ?>");
                            $("#ship_class_boarding").val("<?php echo $ship_class ?>");

                            $("#service").val(service);
                            $("#ticket_number").val(x.ticket);


                }

                else
                {
                    html+=  'Tidak ada data';

                    $("#result").html("");
                    $("#result").html('<div><hr></div><center>Tidak Ada Data</center>');
                    $("#boarding_code").val("");
                    $("#ship_class_boarding").val("");
                }

                // console.log(x);

            },
            complete: function(){
                $('#box').unblock(); 
            }            



        });
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData2(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#btnTicketNumber").on("click",function(x){
            getData();
        });

        $("#ticket_number2").on("keyup",function(e){
            if (e.keyCode === 13) {
                getData();
            }
        });        

    })

    function postData2(url,data){
        $.ajax({
            url         : url,
            data        : data,
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                if(json.code == 1)
                {
                    closeModal();
                    toastr.success(json.message, 'Sukses');
                    $('#dataTables').DataTable().ajax.reload( null, false );
                    $('#dataTables2').DataTable().ajax.reload( null, false );
                    $('#dataTables3').DataTable().ajax.reload( null, false );
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
</script>