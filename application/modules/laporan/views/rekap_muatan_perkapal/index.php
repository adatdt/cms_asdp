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
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tanggal Shift</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>">
                                            </div>    

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                
                                            <?php
                                                    echo form_dropdown("port",$port,"",' id="port" class="form-control js-data-example-ajax select2" ')                                              
                                                ?>                
                                            </div> 
                                                
                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Kapal</div>
                                                <select id="ship" class="form-control js-data-example-ajax select2 input-small" dir="" name="ship">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($ship as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                            <!-- belum naik tiket MANUAL -->
<!-- 
                                            
                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">TIPE JADWAL TIKET </div>
                                                <?= form_dropdown("ticketType",$ticketType,"",' id="ticketType" class="form-control js-data-example-ajax select2"') ?>
                                            </div>                                             -->
                                                                                       

                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered table-striped   table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>WAKTU <br> BUKA LAYANAN</th>
                                            <th>KAPAL</th>
                                            <th>SCHEDULE CODE</th>
                                            <th>PELABUHAN</th>
                                            <th>KELAS</th>
                                            <th>DERMAGA</th>
                                            <th>TANGGAL SHIFT</th>
                                            <th>NAMA SHIFT</th>
                                            <!-- <th>TIPE JADWAL TIKET</th> -->
                                            <th>AKSI</th>
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

<script type="text/javascript">

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporan/rekap_muatan_perkapal') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.port = document.getElementById('port').value;
                    d.ship = document.getElementById('ship').value;
                    // d.ticketType = document.getElementById('ticketType').value; // belum naik tiket manual
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "open_boarding_date", "orderable": true, "className": "text-center"},
                    {"data": "ship_name", "orderable": true, "className": "text-left"},
                    {"data": "schedule_code", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                    {"data": "dock_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_date", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-left"},
                    // {"data": "ticket_type", "orderable": true, "className": "text-left"}, // belum naik tiket manual
                    {"data": "actions", "orderable": false, "className": "text-center","width" : "15%"},
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

    
    jQuery(document).ready(function () {
        table.init();

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateTo').datepicker('setStartDate', e.date);
            table.reload();
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            startDate: $('#dateFrom').val(),
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateFrom').datepicker('setEndDate', e.date);
            table.reload();
        });

        $("#port").change(function(){
            table.reload();
        });

        $("#ship").change(function(){
            table.reload();
        });

        $("#ticketType").change(function(){
            table.reload();
        });        

        

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);
        
    });
</script>
