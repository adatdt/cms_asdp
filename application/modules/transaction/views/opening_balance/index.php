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

        <?php $now = date("Y-m-d"); $last_week = date('Y-m-d',strtotime("-1 days"))?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title ?></div>
                </div>
                <div class="portlet-body">

                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">


                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon ">Tanggal</div>
                                    <input type="text" class="form-control date input-small" name="dateFrom" placeholder="YYY-MM-DD"  id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control date input-small" name="dateTo" placeholder="YYY-MM-DD"  id="dateTo" value="<?php echo $now; ?>" readonly>

                                </div> 

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                        <?php if($row_port!=0) {} else { ?>
                                        <option value="">Pilih</option>
                                        <?php } foreach($port as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div> 

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Shift</div>
                                    <select id="shift" class="form-control js-data-example-ajax select2 input-small" dir="" name="shift">
                                        <option value="">Pilih</option>
                                        <?php foreach($shift as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name) ?></option>
                                        <?php } ?>
                                    </select>
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
                                        <a class="label label-primary " data-toggle="tab" href="#tab1">Dinas POS</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#tab2">Dinas CS</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#tab3">Dinas PTC/ STC</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#tab4">Verifikator</a>
                                </li>
                                <li class="nav-item">
                                        <a class="label label-primary " data-toggle="tab" href="#tab5">Comand Center</a>
                                </li>                                                                          
                            </ul>
          
                            <div class="tab-content " >

                                <div class="tab-pane active" id="tab1" role="tabpanel" >                                         

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL <br>TRANSAKSI</th>
                                                <th>KODE OB</th>
                                                <th>USERNAME</th>
                                                <th>NAMA</th>
                                                <th>SHIFT <br>NAME</th>
                                                <th>PELABUHAN</th>
                                                <th>KODE <br>PENUGASAN</th>
                                                <th>LOKET</th>
                                                <th>TOTAL <br>CASH (Rp.)</th>
                                                <th>TOTAL <br>NON CASH (Rp.)</th>
                                                <!-- <th>TOTAL <br>CASH + NON CASH (Rp.)</th> -->
                                                <th>STATUS</th>
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

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables2">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL <br>PENUGASAN</th>
                                                <th>KODE OB</th>
                                                <th>USERNAME</th>
                                                <th>NAMA</th>
                                                <th>SHIFT <br>NAME</th>
                                                <th>PELABUHAN</th>
                                                <th>KODE <br>PENUGASAN</th>
                                                <th>LOKET</th>
                                                <th>STATUS</th>
                                                <th class="center">AKSI</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>

                                <div class="tab-pane" id="tab3" role="tabpanel">

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables3">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL <br>PENUGASAN</th>
                                                <th>KODE OB</th>
                                                <th>USERNAME</th>
                                                <th>NAMA</th>
                                                <th>SHIFT <br>NAME</th>
                                                <th>PELABUHAN</th>
                                                <th>KODE <br>PENUGASAN</th>
                                                <th>LOKET</th>
                                                <th>STATUS</th>
                                                <th class="center">AKSI</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>       
                                
                                <div class="tab-pane" id="tab4" role="tabpanel">

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables4">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL <br>PENUGASAN</th>
                                                <th>KODE OB</th>
                                                <th>USERNAME</th>
                                                <th>NAMA</th>
                                                <th>SHIFT <br>NAME</th>
                                                <th>PELABUHAN</th>
                                                <th>KODE <br>PENUGASAN</th>
                                                <th>LOKET</th>
                                                <th>STATUS</th>
                                                <th class="center">AKSI</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div> 
                                
                                <div class="tab-pane" id="tab5" role="tabpanel">

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables5">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>TANGGAL <br>PENUGASAN</th>
                                                <th>KODE OB</th>
                                                <th>USERNAME</th>
                                                <th>NAMA</th>
                                                <th>SHIFT <br>NAME</th>
                                                <th>PELABUHAN</th>
                                                <th>KODE <br>PENUGASAN</th>
                                                <th>LOKET</th>
                                                <th>STATUS</th>
                                                <th class="center">AKSI</th>
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

<?php include "fileJs.php"; ?>
<script type="text/javascript">
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    }); 

    var table= {
        loadData: function() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/opening_balance') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shift = document.getElementById('shift').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                // "searching":false,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "trx_date", "orderable": true, "className": "text-left"},
                    {"data": "ob_code", "orderable": true, "className": "text-left"},
                    {"data": "username", "orderable": true, "className": "text-left"},
                    {"data": "full_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-center"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "code", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-center"},
                    {"data": "total_cash", "orderable": true, "className": "text-right"},
                    {"data": "total_non_tunai", "orderable": true, "className": "text-right"},
                    // {"data": "grand_total", "orderable": true, "className": "text-right"},
                    {"data": "status", "orderable": true, "className": "text-center"},
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
                "order": [[ 1, "desc" ]],
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
                fnDrawCallback: function(allRow){
                    $('#searching').button('reset');
                    // console.log(allRow.json);
                    let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                    let getToken = allRow.json[getTokenName];			

                    csfrData[getTokenName] = getToken;
                    if( allRow.json[getTokenName] == undefined )
                    {
                        csfrData[allRow.json['csrfName']] = allRow.json['tokenHash'];
                    }							
                    $.ajaxSetup({
                        data: csfrData
                    });                    
                }
            });

            // $('#export_tools > li > a.tool-action').on('click', function() {
            //     var data_tables = $('#dataTables').DataTable();
            //     var action = $(this).attr('data-action');

            //     data_tables.button(action).trigger();
            // });
        },

        reload: function() {
            $('#dataTables').DataTable().ajax.reload(null, false);
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };

    jQuery(document).ready(function () {

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        table.init();
        table2.init();
        table3.init();
        table4.init();
        table5.init();

        // $('#searching').click(function(){
        //     $(this).button('loading');
        //     table.reload();
        // });

        $('#dateTo').change(function(){
            table.reload();
            table2.reload();
            table3.reload();
            table4.reload();
            table5.reload();
        });

        $('#dateFrom').change(function(){
            table.reload();
            table2.reload();
            table3.reload();
            table4.reload();
            table5.reload();
        });

        $('#port').change(function(){
            table.reload();
            table2.reload();
            table3.reload();
            table4.reload();
            table5.reload();
        });

        $('#shift').change(function(){
            table.reload();
            table2.reload();
            table3.reload();
            table4.reload();
            table5.reload();
        });
       
    });

</script>
