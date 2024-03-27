<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_title . '">' . $title . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>                
                <li>
                    <span><?php echo $title2; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php $now = date("Y-m-d"); $last_week = date('Y-m-d',strtotime("-1 days"))?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title2 ?></div>
                </div>
                <div class="portlet-body">

                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">


                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon ">Tanggal Keberangkatan</div>
                                    <input type="text" class="form-control date input-small" name="dateFrom" placeholder="YYY-MM-DD"  id="departDate" value="<?= $departDate; ?>" readonly>
                                    
                                </div>
                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <?= form_dropdown("port",$port,$portId,'class="form-control js-data-example-ajax select2 input-small" dir="" id="port" '); ?>                                    
                                </div> 

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Layanan</div>
                                    <?= form_dropdown("shipClass",$shipClass,$shipClassId,'class="form-control js-data-example-ajax select2 input-small" dir="" id="shipClass" '); ?>
                                </div>

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
                                                <a href="javascript:;" onclick="changeSearch('No Tiket','ticketNumber')">No Tiket</a>
                                            </li>                                            
                                            <li>
                                                <a href="javascript:;" onclick="changeSearch('Nama Pemesan','costName')">Nama Pemesan</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="changeSearch('Nama Penumpang','passName')">Nama Penumpang</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="bookingCode" name="searchData" id="searchData"> 
                                </div>

                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>                                            


                            </div>

                        </div>
                    </div>                 

                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <ul class="nav nav-tabs " role="tablist">
                                <li class="nav-item active">
                                        <a class="label label-primary " data-toggle="tab" href="#tab1">Kenadaraan</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#tab2">Penumpang</a>
                                </li>

                            </ul>
          
                            <div class="tab-content " >

                                <div class="tab-pane active" id="tab1" role="tabpanel" >

                                    <div class="pull-right btn-add-padding">
                                        <?= $btn_excel." ".$btn_pdf." ".$btn_csv; ?>
                    
                                    </div>

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>NO TIKET</th>
                                                <th>KODE BOOKING</th>
                                                <th>NAMA PEMESAN</th>
                                                <th>NO TELEPON</th>
                                                <th>NAMA PENUMPANG</th>
                                                <th>NIK</th>
                                                <th>ASAL</th>
                                                <th>LAYANAN</th>
                                                <th>TANGGAL DAN JAM MASUK PELABUHAN</th>
                                                <th>GOLONGAN</th>
                                                <th>NO POLISI</th>
                                                <th>TIPE PEMBAYARAN</th>
                                                <th>CHANNEL</th>
                                                <th>TARIF TICKET</th>
                                                <th>BIAYA ADMIN</th>
                                                <th>TOTAL BAYAR</th>
                                                <th>STATUS</th>
                                                <th>PEMESANAN</th>
                                                <th>PEMBAYARAN</th>
                                                <th>CETAK BOARDING PASS</th>
                                                <th>VALIDASI</th>
                                                <th class="center">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                AKSI
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                                <div class="tab-pane" id="tab2" role="tabpanel">

                                    <div class="pull-right btn-add-padding">
                                        <?= $btn_excel2." ".$btn_pdf2." ".$btn_csv2; ?>
                    
                                    </div>

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables2">
                                        <thead>
                                        <tr>
                                                <th>NO</th>
                                                <th>NO TIKET</th>
                                                <th>KODE BOOKING</th>
                                                <th>NAMA PEMESAN</th>
                                                <th>NO TELEPON</th>
                                                <th>NAMA PENUMPANG</th>
                                                <th>NIK</th>
                                                <th>ASAL</th>
                                                <th>LAYANAN</th>
                                                <th>TANGGAL DAN JAM MASUK PELABUHAN</th>
                                                <th>GOLONGAN</th>
                                                <th>TIPE PEMBAYARAN</th>
                                                <th>CHANNEL</th>
                                                <th>TARIF TICKET</th>
                                                <th>BIAYA ADMIN</th>
                                                <th>TOTAL BAYAR</th>
                                                <th>STATUS</th>
                                                <th>PEMESANAN</th>
                                                <th>PEMBAYARAN</th>
                                                <th>CETAK BOARDING PASS</th>
                                                <th>VALIDASI</th>
                                                <th class="center">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                AKSI
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                </th>
                                        </thead>
                                    </table>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function changeSearch(x,name)
    {
        $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#searchData").attr('data-name', name);
    }

    $(document).ready(function () {

        var table= {
            loadData: function() {
                $('#dataTables').DataTable({
                    "ajax": {
                        "url": "<?php echo site_url('dashboard/data_detail_reservasi_kendaraan') ?>",
                        "type": "POST",
                        "data": function(d) {
                            d.departDate = document.getElementById('departDate').value;
                            d.portId = document.getElementById('port').value;
                            d.shipClassId = document.getElementById('shipClass').value;
                            d.detail = "<?= $detail; ?>";
                            d.searchData = $("#searchData").val();
                            d.searchName = $("#searchData").attr("data-name");
                        },
                    },
                
                    "serverSide": true,
                    "processing": true,
                    "searching":false,
                    "columns": [
                            {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                            {"data": "ticket_number", "orderable": true, "className": "text-left"},
                            {"data": "booking_code", "orderable": true, "className": "text-left"},
                            {"data": "customer_name", "orderable": true, "className": "text-left"},
                            {"data": "phone_number", "orderable": true, "className": "text-left"},
                            {"data": "nama_penumpang", "orderable": true, "className": "text-left"},
                            {"data": "nik", "orderable": true, "className": "text-left"},
                            {"data": "asal", "orderable": true, "className": "text-left"},
                            {"data": "layanan", "orderable": true, "className": "text-left"},
                            {"data": "depart_date", "orderable": true, "className": "text-left"},
                            {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                            {"data": "plat_number", "orderable": true, "className": "text-left"},
                            {"data": "tipe_pembayaran", "orderable": true, "className": "text-left"},
                            {"data": "channel", "orderable": true, "className": "text-left"},
                            {"data": "tarif_ticket", "orderable": true, "className": "text-right"},
                            {"data": "biaya_admin", "orderable": true, "className": "text-right"},
                            {"data": "total_bayar", "orderable": true, "className": "text-right"},
                            {"data": "status_ticket", "orderable": true, "className": "text-center"},
                            {"data": "pemesanan", "orderable": true, "className": "text-center"},
                            {"data": "pembayaran", "orderable": true, "className": "text-center"},                            
                            {"data": "cetak_boarding", "orderable": true, "className": "text-center"},
                            {"data": "validasi", "orderable": true, "className": "text-center"},
                            {"data": "actions", "orderable": true, "className": "text-center"},
                                                        

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
                        var $searchInput = $('div.dataTables_filter input');
                        var data_tables = $('#dataTables').DataTable();
                        $searchInput.unbind();
                        $searchInput.bind('keyup', function (e) {
                            if (e.keyCode == 13 || e.whiche == 13) {
                                data_tables.search(this.value).draw();
                            }
                        });
                    },
                });

                $('#export_tools > li > a.tool-action').on('click', function() {
                    var data_tables = $('#dataTables').DataTable();
                    var action = $(this).attr('data-action');

                    data_tables.button(action).trigger();
                });
            },

            reload: function() {
                $('#dataTables').DataTable().ajax.reload();
            },

            init: function() {
                if (!jQuery().DataTable) {
                    return;
                }

                this.loadData();
            }
        };

        var table2= {
            loadData: function() {
                $('#dataTables2').DataTable({
                    "ajax": {
                        "url": "<?php echo site_url('dashboard/data_detail_reservasi_penumpang') ?>",
                        "type": "POST",
                        "data": function(d) {
                            d.departDate = document.getElementById('departDate').value;
                            d.portId = document.getElementById('port').value;
                            d.shipClassId = document.getElementById('shipClass').value;
                            d.detail = "<?= $detail; ?>";
                            d.searchData = $("#searchData").val();
                            d.searchName = $("#searchData").attr("data-name");
                        },
                    },
                
                    "serverSide": true,
                    "processing": true,
                    "searching":false,
                    "columns": [
                            {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                            {"data": "ticket_number", "orderable": true, "className": "text-left"},
                            {"data": "booking_code", "orderable": true, "className": "text-left"},
                            {"data": "customer_name", "orderable": true, "className": "text-left"},
                            {"data": "phone_number", "orderable": true, "className": "text-left"},
                            {"data": "nama_penumpang", "orderable": true, "className": "text-left"},
                            {"data": "nik", "orderable": true, "className": "text-left"},
                            {"data": "asal", "orderable": true, "className": "text-left"},
                            {"data": "layanan", "orderable": true, "className": "text-left"},
                            {"data": "depart_date", "orderable": true, "className": "text-left"},
                            {"data": "passanger_type_name", "orderable": true, "className": "text-left"},
                            {"data": "tipe_pembayaran", "orderable": true, "className": "text-left"},
                            {"data": "channel", "orderable": true, "className": "text-left"},
                            {"data": "tarif_ticket", "orderable": true, "className": "text-right"},
                            {"data": "biaya_admin", "orderable": true, "className": "text-right"},
                            {"data": "total_bayar", "orderable": true, "className": "text-right"},
                            {"data": "status_ticket", "orderable": true, "className": "text-center"},
                            {"data": "pemesanan", "orderable": true, "className": "text-center"},
                            {"data": "pembayaran", "orderable": true, "className": "text-center"},                            
                            {"data": "cetak_boarding", "orderable": true, "className": "text-center"},
                            {"data": "validasi", "orderable": true, "className": "text-center"},
                            {"data": "actions", "orderable": false, "className": "text-center"},
                                                        
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
                        var $searchInput = $('div.dataTables2_filter input');
                        var data_tables = $('#dataTables').DataTable();
                        $searchInput.unbind();
                        $searchInput.bind('keyup', function (e) {
                            if (e.keyCode == 13 || e.whiche == 13) {
                                data_tables.search(this.value).draw();
                            }
                        });
                    },
                });

                $('#export_tools > li > a.tool-action').on('click', function() {
                    var data_tables = $('#dataTables').DataTable();
                    var action = $(this).attr('data-action');

                    data_tables.button(action).trigger();
                });
            },

            reload: function() {
                $('#dataTables2').DataTable().ajax.reload();
            },

            init: function() {
                if (!jQuery().DataTable) {
                    return;
                }

                this.loadData();
            }
        };

        table.init();
        table2.init();
        

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            table2.reload();
            $('#dataTables').on('draw.dt', function() {
                $('#dataTables2').on('draw.dt', function() {
                    $("#cari").button('reset');
                });
                // $("#cari").button('reset');
            });
        });

        $("#download_excel").click(function(event) {

            let param = `departDate=${document.getElementById('departDate').value}`
            param +=`&portId=${document.getElementById('port').value}`
            param +=`&shipClassId=${document.getElementById('shipClass').value}`
            param +=`&detail=<?= $detail; ?>`
            param +=`&searchData=${$("#searchData").val()}`
            param +=`&searchName=${$("#searchData").attr("data-name")}`
                        
            window.location.href = "<?php echo site_url('dashboard/download_excel_knd?') ?>"+param;
        });

        $("#download_pdf").click(function(event) {

            let param = `departDate=${document.getElementById('departDate').value}`
            param +=`&portId=${document.getElementById('port').value}`
            param +=`&shipClassId=${document.getElementById('shipClass').value}`
            param +=`&detail=<?= $detail; ?>`
            param +=`&searchData=${$("#searchData").val()}`
            param +=`&searchName=${$("#searchData").attr("data-name")}`
  
            window.location.href = "<?php echo site_url('dashboard/download_pdf_knd?') ?>"+param;
        });

        $("#download_csv").click(function(event) {
            let param = `departDate=${document.getElementById('departDate').value}`
            param +=`&portId=${document.getElementById('port').value}`
            param +=`&shipClassId=${document.getElementById('shipClass').value}`
            param +=`&detail=<?= $detail; ?>`
            param +=`&searchData=${$("#searchData").val()}`
            param +=`&searchName=${$("#searchData").attr("data-name")}`
  
            window.location.href = "<?php echo site_url('dashboard/download_csv_knd?') ?>"+param;
        });

        $("#download_excel2").click(function(event) {
            let param = `departDate=${document.getElementById('departDate').value}`
            param +=`&portId=${document.getElementById('port').value}`
            param +=`&shipClassId=${document.getElementById('shipClass').value}`
            param +=`&detail=<?= $detail; ?>`
            param +=`&searchData=${$("#searchData").val()}`
            param +=`&searchName=${$("#searchData").attr("data-name")}`
                        
            window.location.href = "<?php echo site_url('dashboard/download_excel_pnp?') ?>"+param;
        });

        $("#download_pdf2").click(function(event) {
            let param = `departDate=${document.getElementById('departDate').value}`
            param +=`&portId=${document.getElementById('port').value}`
            param +=`&shipClassId=${document.getElementById('shipClass').value}`
            param +=`&detail=<?= $detail; ?>`
            param +=`&searchData=${$("#searchData").val()}`
            param +=`&searchName=${$("#searchData").attr("data-name")}`

            window.location.href = "<?php echo site_url('dashboard/download_pdf_pnp?') ?>"+param;
        });

        $("#download_csv2").click(function(event) {
            let param = `departDate=${document.getElementById('departDate').value}`
            param +=`&portId=${document.getElementById('port').value}`
            param +=`&shipClassId=${document.getElementById('shipClass').value}`
            param +=`&detail=<?= $detail; ?>`
            param +=`&searchData=${$("#searchData").val()}`
            param +=`&searchName=${$("#searchData").attr("data-name")}`

            window.location.href = "<?php echo site_url('dashboard/download_csv_pnp?') ?>"+param;
        });        
        
    });

</script>
