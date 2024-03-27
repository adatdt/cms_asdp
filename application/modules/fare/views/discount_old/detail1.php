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
                    <div class="pull-right btn-add-padding"><!-- <?php echo $btn_add; ?> --></div>
                </div>
                <div class="portlet-body">

                    <div class="form-inline">
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-xs-12">
                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-color-primary uppercase">Kode Discount</div>
                                    <p class="ribbon-content"><?php echo $this->enc->decode($discount_code) ?></p>
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
                                        <a class="label label-primary " data-toggle="tab" href="#fare_passanger">Tarif Discount Penumpang</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#fare_vehicle">Tarif Discount Kendaraan</a>
                                </li>

                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#payment_type">Tipe Pembayaran</a>
                                </li>
                            </ul>
          
                            <div class="tab-content " >
                                <!-- Fare Penumpang-->
                                <div class="tab-pane active" id="fare_passanger" role="tabpanel" >


                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>KODE DISKON</th>
                                                <th>PELABUHAN <br>KEBERANGKATAN</th>
                                                <th>PELABUHAN <br>TUJUAN</th>
                                                <th>JENIS<br>PENUMPANG</th>
                                                <th>TARIF <br>MASUK (Rp.)</th>
                                                <th>TARIF <br>DERMAGA (Rp.)</th>
                                                <th>TARIF <br>IFPRO (Rp.)</th>
                                                <th>BIAYA TANGGUNG <br>JAWAB ANGKUT(ATJP) (Rp.)</th>
                                                <th>ASURANSI <br>JASA (Rp.)</th>
                                                <th>TARIF <br>JASA (Rp.)</th>

                                                <th>HARGA (Rp.)</th>
                                                <th>TIPE KAPAL</th>
                                                <!-- <th>STATUS</th> -->
                                            </tr>
                                        </thead>
                                    </table>

                                </div>

                                <div class="tab-pane" id="fare_vehicle" role="tabpanel">

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables2">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>KODE DISKON</th>
                                                <th>PELABUHAN <br>KEBERANGKATAN</th>
                                                <th>PELABUHAN <br>TUJUAN</th>
                                                <th>GOLONGAN</th>

                                                <th>TARIF <br>MASUK (Rp.)</th>
                                                <th>TARIF <br>DERMAGA (Rp.)</th>
                                                <th>TARIF <br>IFPRO (Rp.)</th>
                                                <th>BIAYA TANGGUNG <br>JAWAB ANGKUT(ATJP) (Rp.)</th>
                                                <th>ASURANSI <br>JASA (Rp.)</th>
                                                <th>TARIF <br>JASA (Rp.)</th>

                                                <th>HARGA (Rp.)</th>
                                                <th>TIPE KAPAL</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                                <div class="tab-pane" id="payment_type" role="tabpanel">

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables3">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>KODE DISKON</th>
                                                <th>TIPE PEMBAYARAN</th>
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
                "url": "<?php echo site_url('fare/discount/fare_passanger') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port = document.getElementById('port').value;
                    // d.team = document.getElementById('team').value;
                    d.discount_code = '<?php echo $discount_code ?>';
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "discount_code", "orderable": true, "className": "text-left"},
                    {"data": "origin_name", "orderable": true, "className": "text-left"},
                    {"data": "destination_name", "orderable": true, "className": "text-left"},
                    {"data": "type_name", "orderable": true, "className": "text-left"},

                    {"data": "entry_fee", "orderable": true, "className": "text-right"},
                    {"data": "dock_fee", "orderable": true, "className": "text-right"},
                    {"data": "ifpro_fee", "orderable": true, "className": "text-right"},
                    {"data": "responsibility_fee", "orderable": true, "className": "text-right"},
                    {"data": "insurance_fee", "orderable": true, "className": "text-right"},
                    {"data": "trip_fee", "orderable": true, "className": "text-right"},


                    {"data": "fare", "orderable": true, "className": "text-right"},
                    {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                    // {"data": "status", "orderable": true, "className": "text-center"},
                    // {"data": "actions", "orderable": false, "className": "text-center"},
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

    },

    reload: function() {
        $('#dataTables').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }
        this.loadData();
    },

};

var table2= {
    loadData: function() {
        $('#dataTables2').DataTable({
            "ajax": {
                "url": "<?php echo site_url('fare/discount/fare_vehicle') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port = document.getElementById('port').value;
                    // d.team = document.getElementById('team').value;
                    d.discount_code = '<?php echo $discount_code ?>';
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "discount_code", "orderable": true, "className": "text-left"},
                    {"data": "origin_name", "orderable": true, "className": "text-left"},
                    {"data": "destination_name", "orderable": true, "className": "text-left"},
                    {"data": "type_name", "orderable": true, "className": "text-left"},
                    

                    {"data": "entry_fee", "orderable": true, "className": "text-right"},
                    {"data": "dock_fee", "orderable": true, "className": "text-right"},
                    {"data": "ifpro_fee", "orderable": true, "className": "text-right"},
                    {"data": "responsibility_fee", "orderable": true, "className": "text-right"},
                    {"data": "insurance_fee", "orderable": true, "className": "text-right"},
                    {"data": "trip_fee", "orderable": true, "className": "text-right"},

                    {"data": "fare", "orderable": true, "className": "text-right"},
                    {"data": "ship_class_name", "orderable": true, "className": "text-left"}
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
            }

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
    },

};

var table3= {
    loadData: function() {
        $('#dataTables3').DataTable({
            "ajax": {
                "url": "<?php echo site_url('fare/discount/payment_type') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port = document.getElementById('port').value;
                    // d.team = document.getElementById('team').value;
                    d.discount_code = '<?php echo $discount_code ?>';
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "discount_code", "orderable": true, "className": "text-left"},
                    {"data": "payment_type", "orderable": true, "className": "text-left"},
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
                var $searchInput = $('div #dataTables3_filter input');
                var data_tables = $('#dataTables3').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            }

        });
    },

    reload: function() {
        $('#dataTables3').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }
        this.loadData();
    },

};


$(document).ready(function () {
    table.init();
    table2.init();
    table3.init();


    
});

</script>
