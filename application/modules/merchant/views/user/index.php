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
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
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
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>USERNAME</th>
                                <th>MERCHANT NAME</th>
                                <th>MERCHANT ID</th>
                                <th>MERCHANT KEY</th>
                                <!-- <th>MERCHANT PREFIX</th> -->
                                <th>KODE MITRA</th>
                                <th>STATUS</th>
                                <th>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    AKSI
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
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
    var TableDatatablesManaged = function() {

        var initTable1 = function() {
            var table = $('#dataTables');

            // begin first table
            table.dataTable({
                "ajax": {
                    "url": "<?php echo site_url('merchant/user') ?>",
                    "type": "POST",
                    "data": function(d) {},
                    "dataSrc": function ( json ) {
                        //Make your callback here.
                        let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                        let getToken = json[getTokenName];
                        csfrData[getTokenName] = getToken;

                        if( json[getTokenName] == undefined )
                        {
                        csfrData[json.csrfName] = json.tokenHash;
                        }
                            
                        $.ajaxSetup({
                            data: csfrData
                        });
                        
                        
                        return json.data;
                    }  
                },
                "serverSide": true,
                "processing": true,
                "columns": [{
                        "data": "number",
                        "orderable": false,
                        "className": "text-center",
                        "width": 20
                    },
                    {
                        "data": "username",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "merchant_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "merchant_id",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "merchant_key",
                        "orderable": true,
                        "className": "text-left"
                    },
                    // {
                    //     "data": "merchant_prefix",
                    //     "orderable": true,
                    //     "className": "text-left"
                    // }, 
                    {
                        "data": "mitra_code",
                        "orderable": true,
                        "className": "text-left"
                    },                                        
                    {
                        "data": "status",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {
                        "data": "actions",
                        "orderable": false,
                        "className": "text-center"
                    }
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
                //           "columnDefs": [
                //               {
                // "targets": [1,2,3],
                //                   render: $.fn.dataTable.render.text()
                //               }
                //           ],
                "order": [
                    [0, "desc"]
                ], // set first column as a default sort by asc

                // users keypress on search data
                "initComplete": function() {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function(e) {
                        if (e.keyCode == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });

        }

        return {
            //main function to initiate the module
            init: function() {
                if (!jQuery().dataTable) {
                    return;
                }
                initTable1();
            }
        };
    }();

    jQuery(document).ready(function() {
        TableDatatablesManaged.init();
    });
</script>