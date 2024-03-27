<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent . '">' . $parent . '</a>'; ?>
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
                    <div class="pull-right btn-add-padding" id="btn_add">
                        <!--<?php echo $btn_add; ?> -->
                            
                        </div>
                </div>
                <div class="portlet-body">
                    <div class="form-inline">
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-xs-12">
                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-color-primary uppercase">Kode Force Majeure</div>
                                    <p class="ribbon-content"><?php echo $force_code ?></p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-xs-12">
                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-color-primary uppercase">Tanggal Force Majeure</div>
                                    <p class="ribbon-content"><?php echo $force_majeure_date ?></p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-xs-12">
                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-color-primary uppercase">Tipe Force Majeure</div>
                                    <p class="ribbon-content"><?php echo $force_type ?></p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-xs-12">
                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-color-primary uppercase">Waktu Perpanjangan</div>
                                    <p class="ribbon-content"><?php echo $extend_param ?> Jam</p>
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
                                        <a class="label label-primary " data-toggle="tab" href="#penumpang">Penumpang</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#kendaraan">Kendaraan</a>
                                </li>
                            </ul>
          
                            <div class="tab-content " >
                                <!-- Data Kendaraan -->
                                <div class="tab-pane active" id="penumpang" role="tabpanel" >


                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>KODE FORCE MAJEURE</th>
                                                <th>KODE BOOKING</th>
                                                <th>NOMER TIKET</th>
                                                <th>NAMA</th>
                                                <th>USIA</th>
                                                <th>JENIS KELAMIN</th>
                                                <th>SERVIS</th>
                                                <th>STATUS TIKET LAMA</th>
                                                <th>TANGGAL EXP CHECK IN LAMA</th>
                                                <th>TANGGAL EXP CHECK IN BARU</th>
                                                <th>TANGGAL EXP GATE IN LAMA</th>
                                                <th>TANGGAL EXP GATE IN BARU</th>
                                                <th>TANGGAL EXP BOARDING LAMA</th>
                                                <th>TANGGAL EXP BOARDING BARU</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>

                                <div class="tab-pane" id="kendaraan" role="tabpanel">

                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables2">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>KODE FORCE MAJEURE</th>
                                                <th>KODE BOOKING</th>
                                                <th>NOMER TIKET</th>
                                                <th>NAMA PENGEMUDI</th>
                                                <th>GOLONGAN</th>
                                                <th>STATUS TIKET LAMA</th>
                                                <th>TANGGAL EXP CHECK IN LAMA</th>
                                                <th>TANGGAL EXP CHECK IN BARU</th>
                                                <th>TANGGAL EXP GATE IN LAMA</th>
                                                <th>TANGGAL EXP GATE IN BARU</th>
                                                <th>TANGGAL EXP BOARDING LAMA</th>
                                                <th>TANGGAL EXP BOARDING BARU</th>
                                            </tr>
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

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/force_majeure/manifest_passanger') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port = document.getElementById('port').value;
                    // d.team = document.getElementById('team').value;
                    d.force_code="<?php echo $force_code?>";
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": true, "className": "text-center" , "width": 5},
                    {"data": "force_majeure_code", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "name", "orderable": true, "className": "text-left"},
                    {"data": "age", "orderable": true, "className": "text-center"},
                    {"data": "gender", "orderable": true, "className": "text-center"},
                    {"data": "service_name", "orderable": true, "className": "text-center"},
                    {"data": "old_ticket_status", "orderable": true, "className": "text-center"},
                    {"data": "old_checkin_expired", "orderable": true, "className": "text-center"},
                    {"data": "new_checkin_expired", "orderable": true, "className": "text-center"},
                    {"data": "old_gatein_expired", "orderable": true, "className": "text-center"},
                    {"data": "new_gatein_expired", "orderable": true, "className": "text-center"},
                    {"data": "old_boarding_expired", "orderable": true, "className": "text-center"},
                    {"data": "new_boarding_expired", "orderable": true, "className": "text-center"},
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

var table2 = {
    loadData: function() {
        $('#dataTables2').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/force_majeure/manifest_vehicle') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port = document.getElementById('port').value;
                    // d.team = document.getElementById('team').value;
                    d.force_code="<?php echo $force_code?>";
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": true, "className": "text-center" , "width": 5},
                    {"data": "force_majeure_code", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "first_name", "orderable": true, "className": "text-left"},
                    {"data": "vehicle_class_name", "orderable": true, "className": "text-center"},
                    {"data": "old_ticket_status", "orderable": true, "className": "text-center"},
                    {"data": "old_checkin_expired", "orderable": true, "className": "text-center"},
                    {"data": "new_checkin_expired", "orderable": true, "className": "text-center"},
                    {"data": "old_gatein_expired", "orderable": true, "className": "text-center"},
                    {"data": "new_gatein_expired", "orderable": true, "className": "text-center"},
                    {"data": "old_boarding_expired", "orderable": true, "className": "text-center"},
                    {"data": "new_boarding_expired", "orderable": true, "className": "text-center"},
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
