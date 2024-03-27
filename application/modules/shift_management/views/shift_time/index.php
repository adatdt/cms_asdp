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
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="port" class="form-control js-data-example-ajax select2" dir="" name="port">
                                        <?php if($row_port!=0) {} else { ?>
                                        <option value="">Pilih</option>
                                        <?php } foreach($port as $key=>$value ) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Shift</div>
                                    <select id="shift" class="form-control js-data-example-ajax select2" dir="" name="shift">
                                        <option value="">Pilih</option>
                                        <?php  foreach($shift as $key=>$value ) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->shift_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>

                        </div>
                    </div>

                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>SHIFT</th>
                                <th>JAM AWAL SHIFT</th>
    							<th>JAM AKHIR SHIFT</th>
                                <th>PELABUHAN</th>
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

<script type="text/javascript">
var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });

    var TableDatatablesManaged = function () {

        var initTable1 = function () {
            var table = $('#dataTables');

            // begin first table
            table.dataTable({
                "ajax": {
                    "url": "<?php echo site_url('shift_management/shift_time') ?>",
                    "type": "POST",
                    "data": function (d) {
                        d.port= document.getElementById('port').value;
                        d.shift= document.getElementById('shift').value;
                    },
                },
                "serverSide": true,
                "processing": true,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "shift_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_login", "orderable": true, "className": "text-center"},
                    {"data": "shift_logout", "orderable": true, "className": "text-center"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "status", "orderable": true, "className": "text-center"},
					{"data": "actions", "orderable": false, "className": "text-center"},
                ],

                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
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

                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "lengthMenu": [
                    [10, 15, 25, -1],
                    [10, 15, 25, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "order": [
                    [0, "desc"]
                ], // set first column as a default sort by asc

                // users keypress on search data
                "initComplete": function () {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13) {
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

        }

        return {
            //main function to initiate the module
            init: function () {
                if (!jQuery().dataTable) {
                    return;
                }
                initTable1();
            },
            reload:function()
            {
                var table=$('#dataTables').DataTable();
                table.ajax.reload( null, false );
            }
        };
    }();

    jQuery(document).ready(function () {
        TableDatatablesManaged.init();

        $("#port").on("change",function(){
            TableDatatablesManaged.reload();
        });

        $("#shift").on("change",function(){
            TableDatatablesManaged.reload();
        });
        
    });
</script>
