<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">
                       
                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <div class="row">

                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">Kode Booking</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" >
                                                    <?php echo $this->enc->decode($id); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>

                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">Total Penumpang</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" id="total"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>

                                <?php if($booking->service_id==1) { ?>
                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">Total Tarif Penumpang</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" id="total_fare"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>
                                <?php } ?>

                                <?php if($booking->service_id==2) { ?>

                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">Total Kendaraan</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" id="total_vehicle"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>

                                <div class="col-md-3">
                                    <!-- BEGIN WIDGET THUMB -->
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">Total Tarif Kendaraan (Rp.)</h4>
                                        <div class="widget-thumb-wrap">
                                            <!-- <i class="widget-thumb-icon bg-green icon-bulb"></i> -->
                                            <div class="widget-thumb-body">
                                                <!-- <span class="widget-thumb-subtitle">USD</span> -->
                                                <span class="widget-thumb-body-stat" data-counter="counterup" id="total_fare_vehicle"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END WIDGET THUMB -->
                                </div>
                                <?php } ?>

                            </div>

                        </div>
                        <div class="col-md-12">
                            <!-- <input class=" form-control input-small pull-right"> -->
                        </div>

                        <?php if($booking->service_id==2) { ?>

                        <div class="col-md-12">
                            <!-- <div class="table-scrollable"> -->
                                <table class="table table-striped table-bordered table-hover" id="dataTables3">
                                    <thead>
                                        <tr>
                                            <th colspan="16" style="text-align: left">DATA KENDARAAN</th>
                                        </tr>
                                        <tr>
                                            <th>NO</th>
                                            <th>NOMER BOOKING</th>
                                            <th>JENIS KENDARAAN</th>
                                            <th>NOMER PLAT</th>
                                            <th>TARIF (Rp.)</th>
                                            <th>TIPE KAPAL</th>
                                            <th>KEBERANGKATAN</th>
                                            <th>TUJUAN</th>
                                            <!-- <th>TANGGAL BERANGKAT</th> -->
                                            <!-- <th>JAM BERANGKAT</th> -->

                                        </tr>
                                    </thead>
                                    <tbody id="data_vehicle">                                
                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
                            <!-- </div> -->
                        </div>

                        <?php } ?>

                        <div class="col-md-12">
                            <!-- <div class="table-scrollable"> -->

                                <table class="table table-striped table-bordered table-hover" id="dataTables2">
                                    <thead>
                                        <tr>
                                            <th colspan="16" style="text-align: left">DATA PENUMPANG <?php echo $booking->service_id==2?"KENDARAAN":"" ?> </th>
                                        </tr>
                                        <tr>
                                            <th>NO</th>
                                            <th>NOMER BOOKING</th>
                                            <th>NOMER TIKET</th>
                                            <th>NAMA</th>
                                            <th>Alamat</th>
                                            <th>JENIS KELAMIN</th>
                                            <th>USIA</th>
                                            <!-- <th>TANGGAL LAHIR</th> -->
                                            <th>SERVIS</th>
                                            <!-- <th>SPESIAL SERVIS</th> -->
                                            <th>TIPE PENUMPANG</th>
                                            <th>TIPE KAPAL</th>
                                            <th>KEBERANGKATAN</th>
                                            <th>TUJUAN</th>
                                            <th>TARIF (Rp.)</th>
                                            <!-- <th>TANGGAL KEBERANGKATAN</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="data_body">                                
                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
                            <!-- </div> -->
                        </div>

                        <div class="col-md-12">
                            <!-- <input class=" form-control input-small pull-right"> -->
                        </div>

                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + '.' + '$2');
        }
        return x1 + x2;
    }

    function get_data(){
        $.ajax({
            type:"post",
            url :"<?php echo site_url()?>sab/listDetail",
            data:"id=<?php echo $id ?>",
            dataType:"json",
            success:function(x){

                var html="";
                var no=1;
                var total=0;
                for(var i=0; i<x.length;i++)
                {
                    html +="<tr>"+
                            "<td>"+no+"</td>"+
                            "<td>"+x[i].booking_code+"</td>"+
                            "<td>"+x[i].ticket_number+"</td>"+
                            "<td>"+x[i].name+"</td>"+
                            "<td>"+x[i].city+"</td>"+
                            "<td>"+x[i].gender+"</td>"+
                            "<td>"+x[i].age+"</td>"+
                            // "<td>"+x[i].birth_date+"</td>"+
                            "<td>"+x[i].service_name+"</td>"+
                            // "<td>"+x[i].special_service_name+"</td>"+
                            "<td>"+x[i].passenger_type_name+"</td>"+
                            "<td>"+x[i].ship_class_name+"</td>"+
                            "<td>"+x[i].origin_name+"</td>"+
                            "<td>"+x[i].destination_name+"</td>"+
                            "<td>"+x[i].fare+"</td>"+
                            // "<td>"+x[i].depart_time+"</td>"+
                            "</tr>";
                            no++;

                            total += x[i].fare << 0;
                }
             // console.log(html);


                $("#data_body").html(html); 
                $("#total").html(x.length); 

                $("#total_fare").html(addCommas(total)); 
            }
        });
    }

    function get_vehicle()
    {
        $.ajax({
            type:"post",
            url :"<?php echo site_url()?>sab/listVehicle",
            data:"id=<?php echo $id ?>",
            dataType:"json",
            success:function(x){

                var html="";
                var no=1;
                var total=0;

                for(var i=0; i<x.length;i++)
                {
                    html +="<tr>"+
                            "<td>"+no+"</td>"+
                            "<td>"+x[i].booking_code+"</td>"+
                            "<td>"+x[i].vehicle_class_name+"</td>"+
                            "<td>"+x[i].id_number+"</td>"+
                            "<td>"+x[i].fare+"</td>"+
                            "<td>"+x[i].ship_class_name+"</td>"+
                            "<td>"+x[i].origin_name+"</td>"+
                            "<td>"+x[i].destination_name+"</td>"+      
                            // "<td>"+x[i].depart_date+"</td>"+
                            // "<td>"+x[i].depart_time+"</td>"+
                            "</tr>";
                            no++;

                            total += x[i].fare << 0;
                }

                $("#data_vehicle").html(html);
                $("#total_vehicle").html(x.length); 
                $("#total_fare_vehicle").html(addCommas(total));
                // console.log(x);

            }
        });
    }

    $(document).ready(function(){

        get_data();
        get_vehicle();

        $("#dataTables2").DataTable({            
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
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
        });

        $("#dataTables3").DataTable({            
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
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
        });
    })


</script>