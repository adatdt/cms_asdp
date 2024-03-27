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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
                        <?php if ($btn_excel) {?>
                        <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <?php } ?>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3" style="padding-left: 0px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                                    <option value="">Semua</option>
                                                    <?php foreach($port as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="padding-left: 0px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">Vending Machine</div>
                                                <select id="vm" class="form-control js-data-example-ajax select2 input-small" dir="" name="vm">
                                                    <option value="">Semua</option>
                                                    <?php foreach($vm as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->terminal_code); ?>"><?php echo $value->terminal_name ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="padding-left: 0px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">Tanggal Shift</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3" style="padding-left: 0px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">Shift</div>
                                                <select id="shift" class="form-control js-data-example-ajax select2 input-small" dir="" name="shift">
                                                    <option value="">Semua</option>
                                                    <?php foreach($shift as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->shift_name ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="padding-left: 0px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">Regu</div>
                                                <select id="regu" class="form-control select2" dir="">
                                                    <option value="">Semua</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="portlet-body">
                                <table class="table table-bordered table-hover table-striped" id="table_penumpang">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>NAMA PERANGKAT</th>
                                            <th>KODE BOOKING</th>
                                            <th>NOMOR TIKET</th>
                                            <th>GOLONGAN</th>
                                            <th>TARIF</th>
                                            <th>METODE PEMBAYARAN</th>
                                            <th>KELAS</th>
                                            <th>TANGGAL SHIFT</th>
                                            <th>SHIFT</th>
                                            <th>REGU</th>
                                            <th>NAMA PENGGUNA JASA</th>
                                            <th>NO IDENTITAS</th>
                                            <th>NAMA KAPAL</th>
                                            <th>TANGGAL KLAIM</th>
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
                                <h4 class="text-right"><b id="total_amount">Total 0</b></h4>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var table = {
    loadData: function() {
        $('#table_penumpang').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporan/penjualan_vm/penumpang') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port = document.getElementById('port').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.shift = document.getElementById('shift').value;
                    d.regu = document.getElementById('regu').value;
                    d.vm = document.getElementById('vm').value;
                },
                complete: function(){
                    $("#cari").button('reset');
                    $("#tabPenumpang").button('reset');
                    $("#table_penumpang").show();
                }
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "terminal_name", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "golongan", "orderable": true, "className": "text-left"},
                    {"data": "tarif", "orderable": true, "className": "text-right"},
                    {"data": "payment_type", "orderable": true, "className": "text-left"},
                    {"data": "kelas", "orderable": true, "className": "text-left"},
                    {"data": "trans_date", "orderable": true, "className": "text-left"},
                    {"data": "shift", "orderable": true, "className": "text-left"},
                    {"data": "regu", "orderable": true, "className": "text-left"},
                    {"data": "customer_name", "orderable": true, "className": "text-left"},
                    {"data": "id_number", "orderable": true, "className": "text-left"},
                    {"data": "ship", "orderable": true, "className": "text-left"},
                    {"data": "naik_kapal", "orderable": true, "className": "text-left"},
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
            fnDrawCallback: function(data) {  
                params = data.oAjaxData;

                if(data.json.recordsTotal){
                    $('#penumpang .btn-download').css('display','inline-block');
                }else{
                    $('#penumpang .btn-download').css('display','none');
                }

                $('#total_amount').html('Total '+data.json.total);
            },
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var searchInput = $('#table_penumpang_filter input');
                var data_tables = $('#table_penumpang').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {

                        data_tables.search(this.value).draw();
                    }
                });
            },
        });
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
    
    jQuery(document).ready(function () {
        $("#port").change(function() {
            $.ajax({
                method: "GET",
                url: "<?php echo site_url('laporan/penjualan_vm/get_vm/') ?>"+$("#port").val(),
                type: "html"
            })
            .done(function( msg ) {
                $("#vm").html(msg);
            });
        });

        $("#port").change(function() {
            $.ajax({
                method: "GET",
                url: "<?php echo site_url('laporan/penjualan_vm/get_regu/') ?>"+$("#port").val(),
                type: "html"
            })
            .done(function( msg ) {
                $("#regu").html(msg);
            });
        });

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateTo').datepicker('setStartDate', e.date)
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            startDate: $('#dateFrom').val(),
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateFrom').datepicker('setEndDate', e.date)
        });

        table.init();

        $("#download_excel").click(function(event){
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var search = $('.dataTables_filter input').val();
            var shift =  $("#shift").val();
            var regu =  $("#regu").val();
            var vm =  $("#vm").val();
            var port =  $("#port").val();

            window.location.href="<?php echo site_url('laporan/penjualan_vm/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&vm="+vm+"&regu="+regu+"&shift="+shift+"&search="+search;
        });

        $("#dateTo").change(function(){
            table.reload();
        });

        $("#dateFrom").change(function(){
            table.reload();
        });

        $("#shift").change(function(){
            table.reload();
        });

        $("#regu").change(function(){
            table.reload();
        });

        $("#vm").change(function(){
            table.reload();
        });

        $("#port").change(function(){
            table.reload();
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $(".menu-toggler").click(function() {
            $('.select2').css('width', '100%');
        });
        
    });
</script>