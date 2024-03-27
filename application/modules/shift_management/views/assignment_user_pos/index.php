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

        <?php $now=date("Y-m-d"); $lastweek=date('Y-m-d',strtotime("-7 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">                               
                            <div class="portlet-body">

                                <div class="col-md-12">
                                    <div class="table-toolbar">
                                        <div class="row">
                                            <div class="col-sm-12 form-inline">

                                                <div class="input-group ">
                                                    <div class="input-group-addon">Tanggal Penugasan</div>
                                                    <input type="text" name="dateTo" id="dateTo" class="form-control date input-small" value="<?php echo $lastweek ?>" readonly>
                                                    <div class="input-group-addon">s/d</div>
                                                    <input type="text" name="dateFrom"  class="form-control date input-small" value="<?php echo date('Y-m-d')?>" id="dateFrom" readonly>
                                                </div>

                                                <div class="input-group select2-bootstrap-prepend">
                                                    <div class="input-group-addon">Pelabuhan</div>
                                                    <select id="port" class="form-control js-data-example-ajax select2" dir=""  name="port">
                                                        <?php if($row_port!=0) {} else { ?>
                                                        <option value="">Pilih</option>
                                                        <?php } foreach($port as $key=>$value ) { ?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>KODE <br>PENUGASAN</th>
                                            <th>TANGGAL <br>PENUGASAN</th>
                                            <th>KODE <br>REGU</th>
                                            <th>NAMA <br>REGU</th>
                                            <th>PELABUHAN</th>
                                            <th>SHIFT</th>
                                            <th>STATUS</th>
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
var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('shift_management/assignment_user_pos') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.port = document.getElementById('port').value;
                },
            },
        
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "code", "orderable": true, "className": "text-left"},
                    {"data": "assignment_date", "orderable": true, "className": "text-left"},
                    {"data": "team_code", "orderable": true, "className": "text-left"},
                    {"data": "team_name", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-left"},
                    {"data": "label", "orderable": true, "className": "text-center"},
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
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#dataTables').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });

            },
            "fnDrawCallback": function(allRow) 
                {
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

    
    $(document).ready(function () {
        table.init();


        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        })

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // startDate: $('#datefrom').val(),
            // endDate: new Date(),
        })
        
        $("#dateFrom").on("change",function(){
            table.reload();
        });

        $("#dateTo").on("change",function(){
            table.reload();
        });

        $("#port").on("change",function(){
            table.reload();
        });


    });
</script>