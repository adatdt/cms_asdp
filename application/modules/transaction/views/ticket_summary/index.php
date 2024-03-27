<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php 
        if ($booking_date){
            $now       = date('Y-m-d', strtotime($booking_date));
            $last_week = date('Y-m-d', strtotime($booking_date));
        }else{
            $now       = date("Y-m-d");
            $last_week = date('Y-m-d', strtotime("-0 days"));
        }
        ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <!-- <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div> -->
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->

                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                           <!--  <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tanggal Booking</div>
                                                <input type="text" class="form-control  input-small" id="dateFrom" value="<?php echo $last_week; ?>">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>">
                                            </div> -->

                                            <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnDataDate' >Tanggal Booking
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearchDate('Tanggal Booking','createdBooking')">Tanggal Booking</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearchDate('Tanggal Payment','createdPayment')">Tanggal Payment</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearchDate('Tanggal Check In','createdCheckin')">Tanggal Check In</a>
                                                        </li> 
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearchDate('Tanggal Boarding','createdBoarding')">Tanggal Boarding</a>
                                                        </li>  
                                                    </ul>
                                                </div>
                                               
                                                <input type="text" class="form-control  input-small" data-name-date="createdBooking" id="dateFrom" value="<?php echo $last_week; ?>">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>">
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>

                                                <?php echo form_dropdown('port', $port, $selectedPort, ' id="port" class="form-control js-data-example-ajax select2 input-small" ') ?>

<!--                                                 <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                                    <option value="">All</option>
                                                    <?php foreach ($port as $key => $value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select> -->

                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tipe Pembayaran</div>
                                                <?php echo form_dropdown('', $payment_type, '', 'id="payment_type" class="form-control select2"'); ?>
                                            </div>

                                            <div class="col-sm-12 form-inline" style="margin: 5px 0;"></div>

                                            <div class="input-group select2-bootstrap-prepend divChannel"  >
                                                <div class="input-group-addon">Channel</div>
                                                <?php echo form_dropdown('', $channel, '', 'id="channel" class="form-control select2"'); ?>
                                            </div>
                                            <div class="input-group select2-bootstrap-prepend divMerchant" ></div>

                                            <div class="input-group select2-bootstrap-prepend divOutlet" ></div>

                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Booking
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearch('Nomor Invoice','noInvoice')">Nomor Invoice</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearch('Nomer Identitas','noIdentitas')">Nomer Identitas</a>
                                                        </li>

                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearch('No. Tiket','ticketNumber')">No. Tiket</a>
                                                        </li>

                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearch('Nomer Plat','platNo')">Nomer Plat</a>
                                                        </li>
<!-- 
                                                        <li>
                                                            <a href="javascript:;" onclick="changeSearch('Tanggal Booking','createdBooking')">Tanggal Booking</a>
                                                        </li> -->
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="bookingCode"  id="cari" name="cari">
                                            </div>

                                            <div class="input-group pad-top">
                                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="btnSearch" >
                                                    <span class="ladda-label">Cari</span>
                                                    <span class="ladda-spinner"></span>
                                                </button>
                                            </div>

                                        </div>

                                    </div>
                                </div>


                                <div class="kt-portlet__body" style="padding-top: 20px">
                                    <ul class="nav nav-tabs" role="tablist">

                                        <?php if ($service == 1) { ?> 
                                            <li class="nav-item active" >
                                        <?php  } else if (!$service){  ?>
                                            <li class="nav-item active" >
                                        <?php  } else {  ?>
                                            <li class="nav-item " >
                                        <?php  } ?> 

                                            <a id="tabPenumpang" class="label label-primary" data-toggle="tab" data-target="#penumpang" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">
                                            <!-- <a id="tabPenumpang" class="label label-primary" data-toggle="tab" data-target="#penumpang" > -->
                                            Data Pejalan Kaki</a>
                                        </li>

                                        <?php if ($service == 2) { ?> 
                                            <li class="nav-item active" >
                                        <?php   } else {  ?>
                                            <li class="nav-item " >
                                        <?php } ?>

                                            <a id="tabKendaraan" class="label label-primary" data-toggle="tab" data-target="#kendaraan" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">
                                            Data Kendaraan</a>
                                        </li>

                                    </ul>
                                    <button id="excelkita" class="btn btn-sm btn-default" style="display:none"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>
                                </div>

                                <div class="tab-content " >

                                    <!-- tab data penumpang -->
                                <?php if ($service == 1) { ?> 
                                    <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: 10px">
                                <?php   } else if (!$service) {  ?>
                                    <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: 10px">
                                <?php   } else {  ?>
                                    <div class="tab-pane " id="penumpang" role="tabpanel" style="padding: 10px">
                                <?php } ?> 
                                   
                                        <table class="table table-bordered table-striped table-hover" id="table_penumpang" hidden>
                                            <thead>
                                                <tr>
                                                    <th colspan="16" style="text-align: left">DATA PEJALAN KAKI</th>
                                                </tr>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>NOMOR TIKET</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>NOMOR INVOICE</th>
                                                    <th>TANGGAL BERANGKAT</th>
                                                    <th>NAMA PEMESAN</th>
                                                    <th>NIK</th>
                                                    <th>ASAL</th>
                                                    <th>KELAS</th>
                                                    <th>TIPE PEMBAYARAN</th>
                                                    <th>CHANNEL</th>
                                                    <th>MERCHANT</th>
                                                    <th>TARIF</th>
                                                    <th>PEMESANAN</th>
                                                    <th>OUTLET ID</th>
                                                    <th>PEMBAYARAN</th>
                                                    <th>CETAK BOARDING PASS</th>
                                                    <th>GATE IN</th>
                                                    <th>VALIDASI</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>

                                    <!-- Data Kendaraan -->

                                <?php if ($service == 2) { ?> 
                                    <div class="tab-pane active" id="kendaraan" role="tabpanel" style="padding: 10px" >
                                <?php   } else {  ?>
                                    <div class="tab-pane " id="kendaraan" role="tabpanel" style="padding: 10px" >
                                <?php } ?> 
                                
                                        <table class="table table-bordered table-striped   table-hover" id="table_kendaraan" hidden>
                                            <thead>
                                                <tr>
                                                    <th colspan="21" style="text-align: left">DATA KENDARAAN</th>
                                                </tr>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>NOMOR TIKET</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>NOMOR INVOICE</th>
                                                    <th>TANGGAL BERANGKAT</th>
                                                    <th>NAMA PEMESAN</th>
                                                    <th>NIK</th>
                                                    <th>PLAT NOMOR</th>
                                                    <th>ASAL</th>
                                                    <th>KELAS</th>
                                                    <th>GOLONGAN</th>
                                                    <th>TIPE PEMBAYARAN</th>
                                                    <th>CHANNEL</th>
                                                    <th>MERCHANT</th>
                                                    <th>TARIF</th>
                                                    <th>PANJANG DARI SENSOR</th>
                                                    <th>TINGGI DARI SENSOR</th>
                                                    <th>LEBAR DARI SENSOR</th>
                                                    <th>BERAT DARI TIMBANGAN</th>
                                                    <th>PEMESANAN</th>
                                                    <th>OUTLET ID</th>
                                                    <th>PEMBAYARAN</th>
                                                    <th>CETAK BOARDING PASS</th>
                                                    <th>VALIDASI</th>
                                                    <!-- <th>PANJANG</th>
                                                    <th>TINGGI</th>
                                                    <th>LEBAR</th>
                                                    <th>BERAT</th> -->
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>
<?php include "fileJs.php" ?>
<script type="text/javascript">
    const myData = new MyData();
    formatDate=(date)=> {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }


    changeSearch=(x,name)=>
    {
        $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#cari").attr('data-name', name);

    }

    changeSearchDate=(x,name)=>
    {
        $("#btnDataDate").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#dateFrom").attr('data-name-date', name);

    }

var initDT= false;
// var initPnp = false;
// var initKnd = false;


var target = '#penumpang';
var table_penumpang= {
    loadData: function() {
        $('#table_penumpang').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_summary/penumpang') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.port = document.getElementById('port').value;
                    d.payment_type = document.getElementById('payment_type').value;
                    d.channel = document.getElementById('channel').value;
                    d.merchant = $("#merchant").val();
                    d.outletId = $("#outletId").val();
                    d.cari = document.getElementById('cari').value;
                    d.searchName = $("#cari").attr('data-name');
                    d.searchNameDate = $("#dateFrom").attr('data-name-date');
                },
                complete: function(){
                    $("#btnSearch").button('reset');
                    $("#tabPenumpang").button('reset');
                    $("#table_penumpang").show();
                }
            },

            "serverSide": true,
            "processing": true,
            "searching": false,
            "dom": "<'row'<'col-md-12 col-sm-12'i>r><'table-responsive't><'row'<'col-md-12 col-sm-12'pl>>",
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "ticket_number", "orderable": false, "className": "text-left"},
                    {"data": "booking_code", "orderable": false, "className": "text-left"},
                    {"data": "trans_number", "orderable": false, "className": "text-left"},
                    {"data": "depart_date", "orderable": false, "className": "text-left"},
                    {"data": "customer", "orderable": false, "className": "text-left"},
                    {"data": "id_number", "orderable": false, "className": "text-left"},
                    {"data": "origin", "orderable": false, "className": "text-left"},
                    {"data": "ship_class", "orderable": false, "className": "text-left"},
                    {"data": "payment_type", "orderable": false, "className": "text-left"},
                    {"data": "channel", "orderable": false, "className": "text-left"},
                    {"data": "merchant_name", "orderable": false, "className": "text-center"},
                    {"data": "fare", "orderable": false, "className": "text-right"},
                    {"data": "pemesanan", "orderable": false, "className": "text-center"},
                    {"data": "outlet_id", "orderable": false, "className": "text-center"},
                    {"data": "pembayaran", "orderable": false, "className": "text-center"},
                    {"data": "cetak_boarding", "orderable": false, "className": "text-center"},
                    {"data": "gate_in", "orderable": false, "className": "text-center"},
                    {"data": "validasi", "orderable": false, "className": "text-center"},
            ],
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
            "initComplete": function () {
                var searchInput = $('div.table_penumpang_filter input');
                var data_tables = $('#table_penumpang').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });

        // $('#export_tools > li > a.tool-action').on('click', function() {
        //     var data_tables = $('#dataTables').DataTable();
        //     var action = $(this).attr('data-action');

        //     data_tables.button(action).trigger();
        // });
    },

    reload: function() {
        $('#table_penumpang').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var table_kendaraan= {
    loadData: function() {
        $('#table_kendaraan').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/ticket_summary/kendaraan') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.port = document.getElementById('port').value;
                    d.payment_type = document.getElementById('payment_type').value;
                    d.channel = document.getElementById('channel').value;
                    d.merchant = $("#merchant").val();
                    d.outletId = $("#outletId").val();
                    d.cari = document.getElementById('cari').value;
                    d.searchName=$("#cari").attr('data-name');
                    d.searchNameDate = $("#dateFrom").attr('data-name-date');
                },
                complete: function(){
                    $("#btnSearch").button('reset');
                    $("#tabKendaraan").button('reset');
                    $("#table_kendaraan").show();
                }
            },

            "serverSide": true,
            "processing": true,
            "searching": false,
            "dom": "<'row'<'col-md-12 col-sm-12'i>r><'table-responsive't><'row'<'col-md-12 col-sm-12'pl>>",
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "ticket_number", "orderable": false, "className": "text-left"},
                    {"data": "booking_code", "orderable": false, "className": "text-left"},
                    {"data": "trans_number", "orderable": false, "className": "text-left"},
                    {"data": "depart_date", "orderable": false, "className": "text-left"},
                    {"data": "customer", "orderable": false, "className": "text-left"},
                    {"data": "nik", "orderable": false, "className": "text-left"},
                    {"data": "plat", "orderable": false, "className": "text-left"},
                    {"data": "origin", "orderable": false, "className": "text-left"},
                    {"data": "ship_class", "orderable": false, "className": "text-left"},
                    {"data": "vehicle_class", "orderable": false, "className": "text-left"},
                    {"data": "payment_type", "orderable": false, "className": "text-left"},
                    {"data": "channel", "orderable": false, "className": "text-left"},
                    {"data": "merchant_name", "orderable": false, "className": "text-center"},
                    {"data": "fare", "orderable": false, "className": "text-right"},
                    {"data": "length_cam", "orderable": false, "className": "text-left"},
                    {"data": "height_cam", "orderable": false, "className": "text-left"},
                    {"data": "width_cam", "orderable": false, "className": "text-left"},
                    {"data": "weighbridge", "orderable": false, "className": "text-left"},
                    {"data": "pemesanan", "orderable": false, "className": "text-center"},
                    {"data": "outlet_id", "orderable": false, "className": "text-center"},
                    {"data": "pembayaran", "orderable": false, "className": "text-center"},
                    {"data": "cetak_boarding", "orderable": false, "className": "text-center"},
                    {"data": "validasi", "orderable": false, "className": "text-center"},
                    // {"data": "length", "orderable": false, "className": "text-left"},
                    // {"data": "height", "orderable": false, "className": "text-left"},
                    // {"data": "width", "orderable": false, "className": "text-left"},
                    // {"data": "weight", "orderable": false, "className": "text-left"},
            ],
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
            "initComplete": function () {
                var searchInput = $('div.table_kendaraan_filter input');
                var data_tables = $('#table_kendaraan').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });

        // $('#export_tools > li > a.tool-action').on('click', function() {
        //     var data_tables = $('#dataTables').DataTable();
        //     var action = $(this).attr('data-action');

        //     data_tables.button(action).trigger();
        // });
    },

    reload: function() {
        $('#table_kendaraan').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }

    jQuery(document).ready(function () {

        table_kendaraan.init();
        table_penumpang.init();
        $("#tabPenumpang").button('loading');
        $("#tabKendaraan").button('loading');
        $("#btnSearch").button('loading');
        $("#excelkita").show();

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
        });


        $("#dateFrom").change(function() {

            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo').datepicker('remove');

              // Re-int with new options
            $('#dateTo').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datepicker("update")
            // table.reload();
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        // table_penumpang.init();
        // table_kendaraan.init();

        $("#excelkita").on("click",function(e){
            port = $("#port").val();
            payment_type = $("#payment_type").val();
            channel = $("#channel").val();
            cari = $("#cari").val();
            dateFrom = $("#dateFrom").val();
            dateTo = $("#dateTo").val();
            searchName=$("#cari").attr('data-name');

            if(target == '#kendaraan'){
                url = "<?php echo site_url('transaction/ticket_summary/excel_kendaraan?port=') ?>";
            }else{
                url = "<?php echo site_url('transaction/ticket_summary/excel_penumpang?port=') ?>";
            }

            if (port != null) {
                window.location = url+port+
                "&payment_type=" +payment_type+
                "&channel=" +channel+
                "&cari=" +cari+
                "&searchName=" +searchName+
                "&dateFrom=" +dateFrom+
                "&dateTo=" +dateTo+
                "&merchant=" + $("#merchant").val() +
                "&outletId=" + $("#outletId").val() +
                "&pelabuhan="+$('#port').find(":selected").text();
            }
        });

        $("#btnSearch").on("click",function(e){
            $(this).button('loading');
            $("#tabPenumpang").button('loading');
            $("#tabKendaraan").button('loading');
            e.preventDefault();
            $("#excelkita").show();
            table_kendaraan.reload();
            table_penumpang.reload();
        });
    });

    $(document).on('click', '[data-toggle="tab"]', function(){
        target = $(this).data('target');
    });

    // $(".divMerchant").hide();
    myData.getMerchant()
    $(".divOutlet").hide();

    $(".divMerchant").show();
    /*
    $("#channel").on('change', function(){
        const getSelected = $( "#channel option:selected" ).text();
        // console.log(getSelected.toLowerCase().trim())
        if(getSelected.toLowerCase().trim() == 'b2b')
        {
            myData.getMerchant()
            $(".divMerchant").show();
        }
        else
        {
            $(".divMerchant").html("").hide();
            $(".divOutlet").html("").hide();
        }
    })
    */

    $("#cari").focus();
    var req = '<?php echo $cari ?>';

    if(req){
        $("#cari").val(req);
    }


</script>