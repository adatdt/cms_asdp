<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">
                       
                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <div class="row">

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Nama Kapal</div>
                                        <p class="ribbon-content"><?php echo $get_ship_name->ship_name; ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Status Operator Kapal</div>
                                        <p class="ribbon-content"><?php echo $status_kapal;  ?></p>
                                    </div>
                                </div>

<!--                                 <div class="col-lg-2 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Status syahbandar</div>
                                        <p class="ribbon-content" id="status"><?php echo $status;  ?></p>
                                    </div>
                                </div> -->



<!--                                 <div class="col-lg-2 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Total Penumpang</div>
                                        <p class="ribbon-content"><?php echo $passanger_count; ?></p>
                                    </div>
                                </div> -->

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Total Penumpang</div>
                                        <p class="ribbon-content"><?php echo $passanger_count; ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Total Kendaraan</div>
                                        <p class="ribbon-content"><?php echo $vehicle_count;  ?></p>
                                    </div>
                                </div>

                                <?php 

                                    $tot_pass=array();
                                    foreach ($passanger_vehicle_count as $key => $value) {
                                        $tot_pass[]=$value->tot_pass;
                                    }
                                ?>
                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Total Penumpang Kendaraan</div>
                                        <p class="ribbon-content"><?php echo array_sum($tot_pass); ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-4 col-xs-12"></div>



                            </div>

                        </div>
                        <div class="col-md-12">
   
<!--                             <?php 

                                if($btn_pdf){
                                // jika data suda di approve
                                $btn_kapal==1?$access='enabled':$access='disabled'; 
                            ?>

                                <button class="btn  btn-sm btn-warning " id="btndpdf" <?php echo $access ?> >Download Pdf</button>

                            <?php } ?> -->

                            <?php   if($btn_pdf)
                            {
                                // kondisi jika data suda di approve atau belum
                                $btn_kapal==1?$access=" id='btndpdf' ":$access=" id='btndpbf' onclick='validation()' "; ?>

                                <button class="btn  btn-sm btn-warning " <?php echo $access ?> >Download Pdf</button>
                                <!-- <button class="btn  btn-sm btn-warning " id="btndpdf" >Download Pdf</button> -->

                            <?php } ?>


                        </div>

                        <div class="col-md-12">
                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                      <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item active">
                                            <a class="label label-primary " data-toggle="tab" href="#" data-target="#penumpang">Data Penumpang</a>
                                        </li>

                                        <li class="nav-item">
                                                <a class="label label-primary " data-toggle="tab" href="#kendaraan">Data Kendaraan</a>
                                        </li>
                                        <li class="nav-item">
                                                <a class="label label-primary " data-toggle="tab" href="#penumpang_kendaraan">Data Penumpang Kendaraan</a>
                                        </li>
                                    </ul>                    
                                <div class="tab-content " >

                                    <!-- tab data penumpang -->
                                    <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: 10px">
                                        <?php if ($btn_excel) { ?>
                                        <button class="btn  btn-sm btn-primary" id="btndownload" >Download</button>
                                        <?php } ?>
                                            <p></p>
                                            <table class="table table-bordered table-striped   table-hover" id="table_penumpang" >
                                                <thead>
                                                    <tr>
                                                        <th colspan="16" style="text-align: left">DATA PENUMPANG</th>
                                                    </tr>
                                                    <tr>
                                                        <th>NO</th>
                                                        <th>NOMER BOOKING</th>
                                                        <th>NOMER TIKET</th>
                                                        <th>NOMER ID</th>
                                                        <th>NAMA</th>
                                                        <th>KOTA</th>
                                                        <th>JENIS KELAMIN</th>
                                                        <th>USIA</th>
                                                        <th>SERVIS</th>
                                                        <th>TIPE PENUMPANG</th>
                                                        <th>TIPE KAPAL</th>
                                                        <th>KEBERANGKATAN</th>
                                                        <th>TUJUAN</th>
                                                        <th>TANGGAL BOARDING</th>
                                                        <th>TANGGAL KEBERANGKATAN</th>
                                                        <th>KETERANGAN</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="data_body">
                                                <?php $no1=1; foreach($detail_passanger as $key=>$value ) {  ?>

                                                    <tr>
                                                        <td><?php echo $no1; ?></td>
                                                        <td><?php echo $value->booking_code; ?></td>
                                                        <td><?php echo $value->ticket_number; ?></td>
                                                        <td><?php echo $value->id_number; ?></td>
                                                        <td><?php echo $value->passanger_name; ?></td>
                                                        <td><?php echo $value->city; ?></td>
                                                        <td><?php echo $value->gender; ?></td>
                                                        <td><?php echo $value->age; ?></td>
                                                        <td><?php echo $value->service_name; ?></td>
                                                        <td><?php echo $value->passanger_type_name; ?></td>
                                                        <td><?php echo $value->ship_class_name; ?></td>
                                                        <td><?php echo $value->port_origin; ?></td>
                                                        <td><?php echo $value->port_destination; ?></td>
                                                        <td><?php echo format_dateTime($value->created_on); ?></td>
                                                        <td><?php echo empty($value->sail_date)?"":format_dateTime($value->sail_date); ?></td>
                                                        <td><?php echo $value->manifest_data_from; ?></td>

                                                    </tr>
                                                 <?php $no1++; } ?>                                
                                                </tbody>
                                                <tfoot></tfoot>
                                            </table>
                                        <!-- </div> -->
                                    </div>

                                    <!-- Data Kendaraan -->
                                    <div class="tab-pane " id="kendaraan" role="tabpanel" style="padding: 10px">
                                        <?php if ($btn_excel) { ?>
                                        <button class="btn  btn-sm btn-primary" id="btndownload2" >Download</button>
                                        <?php }?>
                                            <p></p>

                                            <table class="table table-bordered table-striped   table-hover" id="table_kendaraan" >
                                                <thead>
                                                    <tr>
                                                        <th colspan="16" style="text-align: left">DATA KENDARAAN</th>
                                                    </tr>
                                                    <tr>
                                                        <th>NO</th>
                                                        <th>NOMER BOOKING</th>
                                                        <th>NOMER TIKET</th>
                                                        <th>NOMER PLAT</th>
                                                        <th>TOTAL PENUMPANG</th>
                                                        <th>SERVIS</th>
                                                        <th>GOLONGAN</th>
                                                        <th>TIPE KAPAL</th>
                                                        <th>KEBERANGKATAN</th>
                                                        <th>TUJUAN</th>
                                                        <th>TANGGAL BOARDING</th>
                                                        <th>TANGGAL KEBERANGKATAN</th>
                                                        <th>KETERANGAN</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="data_body">
                                                <?php $no2=1; foreach($detail_vehicle as $key=>$value ) {  ?>

                                                    <tr>
                                                        <td><?php echo $no2; ?></td>
                                                        <td><?php echo $value->booking_code; ?></td>
                                                        <td><?php echo $value->ticket_number; ?></td>
                                                        <td><?php echo $value->plate_number; ?></td>
                                                        <td><?php echo $value->total_passanger; ?></td>
                                                        <td><?php echo $value->service_name; ?></td>
                                                        <td><?php echo $value->golongan; ?></td>
                                                        <td><?php echo $value->ship_class_name; ?></td>
                                                        <td><?php echo $value->port_origin; ?></td>
                                                        <td><?php echo $value->port_destination; ?></td>
                                                        <td><?php echo format_dateTime($value->created_on); ?></td>
                                                        <td><?php echo  empty($value->sail_date)?"":format_dateTime($value->sail_date); ?></td>
                                                        <td><?php echo $value->manifest_data_from; ?></td>

                                                    </tr>
                                                 <?php $no2++; } ?>                                
                                                </tbody>
                                                <tfoot></tfoot>
                                            </table>
                                    </div>
                                    <div class="tab-pane" id="penumpang_kendaraan" role="tabpanel" style="padding: 10px">
                                        <?php if ($btn_excel) { ?>
                                        <button class="btn  btn-sm btn-primary" id="btndownload3"x >Download</button>
                                        <?php } ?>
                                            <p></p>
                                            <table class="table table-bordered table-striped   table-hover" id="table_penumpang_kendaraan" >
                                                <thead>
                                                    <tr>
                                                        <th colspan="16" style="text-align: left">DATA PENUMPANG</th>
                                                    </tr>
                                                    <tr>
                                                        <th>NO</th>
                                                        <th>NOMER BOOKING</th>
                                                        <th>NOMER TIKET</th>
                                                        <th>NOMER PLAT</th>
                                                        <th>NOMER ID</th>
                                                        <th>NAMA</th>
                                                        <th>KOTA</th>
                                                        <th>JENIS KELAMIN</th>
                                                        <th>USIA</th>
                                                        <th>SERVIS</th>
                                                        <th>TIPE PENUMPANG</th>
                                                        <th>TIPE KAPAL</th>
                                                        <th>KEBERANGKATAN</th>
                                                        <th>TUJUAN</th>
                                                        <th>TANGGAL BOARDING</th>
                                                        <th>TANGGAL KEBERANGKATAN</th>
                                                        <th>KETERANGAN</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="data_body">
                                                <?php $no3=1; foreach($detail_passanger_vehicle as $key=>$value ) { ?>

                                                    <tr>
                                                        <td><?php echo $no3; ?></td>
                                                        <td><?php echo $value->booking_code; ?></td>
                                                        <td><?php echo $value->ticket_number; ?></td>
                                                        <td><?php echo $value->plate_number; ?></td>
                                                        <td><?php echo $value->id_number; ?></td>
                                                        <td><?php echo $value->passanger_name; ?></td>
                                                        <td><?php echo $value->city; ?></td>
                                                        <td><?php echo $value->gender; ?></td>
                                                        <td><?php echo $value->age; ?></td>
                                                        <td><?php echo $value->service_name; ?></td>
                                                        <td><?php echo $value->passanger_type_name; ?></td>
                                                        <td><?php echo $value->ship_class_name; ?></td>
                                                        <td><?php echo $value->port_origin; ?></td>
                                                        <td><?php echo $value->port_destination; ?></td>
                                                        <td><?php echo format_dateTime($value->created_on); ?></td>
                                                        <td><?php echo  empty($value->sail_date)?"":format_dateTime($value->sail_date); ?></td>
                                                        <td><?php echo $value->manifest_data_from; ?></td>

                                                    </tr>
                                                 <?php $no3 ++; } ?>                                
                                                </tbody>
                                                <tfoot></tfoot>
                                            </table>
                                    </div>
                                </div>      
                            </div>
                        </div>
                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    $("#btndpdf").click(function(event){

        var boarding="<?php echo $code?>";
        window.open("<?php echo site_url('transaction/syahbandar/download_pdf?boarding_code=')?>"+boarding);

    });

    $("#btndownload").click(function(event){

        window.location.href="<?php echo site_url('transaction/syahbandar/download/'.$code) ?>";
    });

    $("#btndownload2").click(function(event){

        window.location.href="<?php echo site_url('transaction/syahbandar/download_vehicle/'.$code) ?>";
    });

    $("#btndownload3").click(function(event){

        window.location.href="<?php echo site_url('transaction/syahbandar/download_vehicle_passanger/'.$code) ?>";
    });

    // function approve()
    // {
    //     var code="<?php echo $code; ?>";
    //     $.ajax({
    //         type:"post",
    //         dataType:"json",
    //         data:"code="+code,
    //         url:"<?php echo site_url()?>transaction/syahbandar/approve",
    //         success:function(x)
    //         {
    //             // console.log(x);
    //             if(x==1)
    //             {
    //                 $("#btnapprove").addAttr('disabled','disabled')
    //             }
    //         }
    //     });
    // }

    function confirmApprove(message){
    alertify.confirm(message, function (e) {
            if(e){
                approveData()
            }
        });
    }

    function approveData(){
    var code="<?php echo $code; ?>";
    $.ajax({
            url         : "<?php echo site_url()?>transaction/syahbandar/approve",
            type        : 'post',
            data        :"code="+code,
            dataType    : 'json',

            // beforeSend: function(){
            //     $.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});
            // },

            success: function(json) {
                if(json.code == 1)
                {
                    $("#status").html("Sudah Approve");
                    $("#btndpdf").removeAttr('disabled');
                    $("#btnapprove").remove();

                    toastr.success(json.message, 'Sukses');
                    // $('#dataTables').DataTable().ajax.reload();
                }
                else
                {
                    toastr.error(json.message, 'Gagal');
                }
            },

            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            },

            // complete: function(){
            //     $.unblockUI();
            // }
        });
    }


    function validation()
    {

        swal.fire({
        type: 'error',
        title: 'Kapal Belum Approve',
        });

    }

    $(document).ready(function(){

    // $("#btnapprove").click(function(event){

    //     approve();
    // });

        $("#table_penumpang").DataTable({

            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                  "processing": "Proses.....",
                  "emptyTable": "Tidak ada data",
                  "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                  "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                  "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                  "lengthMenu": "Menampilkan _MENU_",
                  "search": "Pencarian :",
                  "zeroRecords": "Tidak ditemukan data yang sesuai",
                  "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya",
                    "last": "Terakhir",
                    "first": "Pertama"
                }
            },
            "lengthMenu": [
                [5,10, 25, 50, 100],
                [5,10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "asc" ]],
        });

        $("#table_penumpang_kendaraan").DataTable({

            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                  "processing": "Proses.....",
                  "emptyTable": "Tidak ada data",
                  "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                  "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                  "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                  "lengthMenu": "Menampilkan _MENU_",
                  "search": "Pencarian :",
                  "zeroRecords": "Tidak ditemukan data yang sesuai",
                  "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya",
                    "last": "Terakhir",
                    "first": "Pertama"
                }
            },
            "lengthMenu": [
                [5,10, 25, 50, 100],
                [5, 10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "asc" ]],
        });

        $("#table_kendaraan").DataTable({

            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                  "processing": "Proses.....",
                  "emptyTable": "Tidak ada data",
                  "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                  "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                  "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                  "lengthMenu": "Menampilkan _MENU_",
                  "search": "Pencarian :",
                  "zeroRecords": "Tidak ditemukan data yang sesuai",
                  "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya",
                    "last": "Terakhir",
                    "first": "Pertama"
                }
            },
            "lengthMenu": [
                [5, 10, 25, 50, 100],
                [5, 10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "asc" ]],
            
        });


        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
           .columns.adjust()
           .responsive.recalc();
        });   

    })
</script>