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
        <div class="my-div-body">

            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                    <div class="pull-right btn-add-padding"></div>
                </div>            

                <div class="portlet-body">
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
                                        <a class="label label-primary " data-toggle="tab" href="#tab1">Perpanjang Penumpang</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#tab2">Perpanjang Kendaraan</a>
                                </li>
                            </ul>
          
                            <div class="tab-content " >

                                <div class="tab-pane active" id="tab1" role="tabpanel" >

                                    <table class="table table-bordered table-striped   table-hover" id="dataTables">
                                        <thead>

                                            <tr>
                                                <th>NO</th>
                                                <th>NOMER TIKET</th>
                                                <th>NOMER BOOKING</th>
                                                <th>NAMA</th>
                                                <th>NOMER IDENTITAS</th>
                                                <th>USIA</th>
                                                <th>JENIS KELAMIN</th>
                                                <th>TIPE PENUMPANG</th>
                                                <th>SERVIS</th>
                                                <th>GATE IN EXPIRE <br>LAMA</th>
                                                <th>GATE IN EXPIRE <br>BARU</th>
                                                <th>BOARDING EXPIRE <br>LAMA</th>
                                                <th>BOARDING EXPIRE <br>BARU</th>
                                                <th>PERPANJANGAN/ JAM</th>
                                            </tr>
                                        </thead>
                                        <tfoot></tfoot>
                                    </table>

                                </div>

                                <div class="tab-pane" id="tab2" role="tabpanel">

                                    <table class="table table-bordered table-striped   table-hover" id="dataTables2">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>NOMER TIKET</th>
                                                <th>NOMER BOOKING</th>
                                                <th>PLAT NOMER</th>
                                                <th>NAMA PENGEMUDI</th>
                                                <th>USIA</th>
                                                <th>JENIS KELAMIN</th>
                                                <th>GOLONGAN</th>
                                                <th>SERVIS</th>
                                                <th>GATE IN EXPIRE <br>LAMA</th>
                                                <th>GATE IN EXPIRE <br>BARU</th>
                                                <th>BOARDING EXPIRE <br>LAMA</th>
                                                <th>BOARDING EXPIRE <br>BARU</th>
                                                <th>PERPANJANGAN/ JAM</th>
                                            </tr>
                                        </thead>
                                        <tfoot></tfoot>
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

    var table= {
        loadData: function() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/extend_ticket') ?>",
                    "type": "POST",
                    "data": function(d) {
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "number", "orderable": false, "className": "text-center", "width": 20},
                        {"data": "ticket_number", "orderable": true, "className": "text-left"},
                        {"data": "booking_code", "orderable": true, "className": "text-left"},
                        {"data": "passanger_name", "orderable": true, "className": "text-left"},
                        {"data": "id_number", "orderable": true, "className": "text-left"},
                        {"data": "age", "orderable": true, "className": "text-left"},
                        {"data": "gender", "orderable": true, "className": "text-left"},
                        {"data": "passanger_type_name", "orderable": true, "className": "text-left"},
                        {"data": "service_name", "orderable": true, "className": "text-left"},
                        {"data": "old_gatein_expired", "orderable": true, "className": "text-left"},
                        {"data": "new_gatein_expired", "orderable": true, "className": "text-left"},
                        {"data": "old_boarding_expired", "orderable": true, "className": "text-left"},
                        {"data": "new_boarding_expired", "orderable": true, "className": "text-left"},
                        {"data": "total_time", "orderable": true, "className": "text-left"},
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
                    var $searchInput = $('div #dataTables_filter input');
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
                    "url": "<?php echo site_url('transaction/extend_ticket/dataListVehicle') ?>",
                    "type": "POST",
                    "data": function(d) {
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "number", "orderable": false, "className": "text-center", "width": 20},
                        {"data": "ticket_number", "orderable": true, "className": "text-left"},
                        {"data": "booking_code", "orderable": true, "className": "text-left"},
                        {"data": "plat_number", "orderable": true, "className": "text-left"},
                        {"data": "driver_name", "orderable": true, "className": "text-left"},
                        {"data": "age", "orderable": true, "className": "text-left"},
                        {"data": "gender", "orderable": true, "className": "text-left"},
                        {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                        {"data": "service_name", "orderable": true, "className": "text-left"},
                        {"data": "old_gatein_expired", "orderable": true, "className": "text-left"},
                        {"data": "new_gatein_expired", "orderable": true, "className": "text-left"},
                        {"data": "old_boarding_expired", "orderable": true, "className": "text-left"},
                        {"data": "new_boarding_expired", "orderable": true, "className": "text-left"}, 
                        {"data": "total_time", "orderable": true, "className": "text-left"},                     
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
                    var $searchInput = $('div #dataTables2_filter input');
                    var data_tables = $('#dataTables2').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
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


    $(document).ready(function () {
        table.init();
        table2.init();
    });
</script>
