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

                    <div class="row">
                        <div class="col-sm-12 form-inline">

                           <div class="input-group pad-top">
                                <div class="input-group-btn">
                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nama
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Nama','name')">Nama</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Kota','city')">Kota</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Kode Pelabuhan','portCode')">Kode Pelabuhan</a>
                                        </li>                                                                      
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Profit Center','profitCenter')">Profit Center</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Urutan','ordering')">Urutan</a>
                                        </li>                                                                                                              
<!--                                         <li>
                                            <a href="javascript:;" onclick="changeSearch('ID BMKG','portIdBmkg')">ID BMKG</a>
                                        </li>                               -->

                                    </ul>
                                </div>
                                <!-- /btn-group -->
                                <input type="text" class="form-control" placeholder="Cari Data" data-name="name" name="searchData" id="searchData"> 
                            </div>   
                            <div class="input-group pad-top">
                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                    <span class="ladda-label">Cari</span>
                                    <span class="ladda-spinner"></span>
                                </button>
                            </div>                                                                            
                        </div>

                    </div>                    

                    <p></p>
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA</th>
                                <th>KOTA</th>
                                <th>KODE PELABUHAN</th>
                                <th>PROFIT CENTER</th>
                                <th>MAX BERAT</th>
                                <th>EVENT KHUSUS</th>
                                <th>IFCS</th>
                                <th>ZONA WAKTU</th>
                                <th>URUTAN</th>
                                <th>USERNAME SIWASOPS</th>
                                <th>URL LOGIN SIWASOPS</th>
                                <th>URL SIWASOPS</th>
                                <!-- <th>ID BMKG</th> -->
    							<th>STATUS</th>
                                <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                AKSI
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
    function changeSearch(x,name)
    {
        $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#searchData").attr('data-name', name);

    }
    var TableDatatablesManaged = function () {

        var initTable1 = function () {
            var table = $('#dataTables');

            // begin first table
            table.dataTable({
                "ajax": {
                    "url": "<?php echo site_url('pelabuhan/port') ?>",
                    "type": "POST",
                    "data": function (d) {
                            d.searchData=document.getElementById('searchData').value;
                            d.searchName=$("#searchData").attr('data-name');                        
                    },
                },

                "filter": false,
                "serverSide": true,
                "processing": true,
                "columns": [
                    {"data": "number", "orderable": false, "className": "text-center", "width": 20},
                    {"data": "name", "orderable": true, "className": "text-left"},
                    {"data": "city", "orderable": true, "className": "text-left"},
                    {"data": "port_code", "orderable": true, "className": "text-center"},
                    {"data": "profit_center", "orderable": true, "className": "text-center"},
                    {"data": "weight_limit", "orderable": true, "className": "text-center"},
                    {"data": "cross_class", "orderable": true, "className": "text-center"},
                    {"data": "ifcs", "orderable": true, "className": "text-center"},
                    {"data": "time_zone", "orderable": true, "className": "text-center"},
                    {"data": "order", "orderable": true, "className": "text-center"},
                    // {"data": "port_id_bmkg", "orderable": true, "className": "text-center"},
					{"data": "username_siwasops", "orderable": true, "className": "text-center"},
					{"data": "url_login_siwasops", "orderable": true, "className": "text-center"},
					{"data": "url_siwasops", "orderable": true, "className": "text-center"},
					{"data": "status", "orderable": true, "className": "text-center"},
                    {"data": "actions", "orderable": false, "className": "text-center"}
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
            });

        }

        return {
            //main function to initiate the module
            init: function () {
                if (!jQuery().dataTable) {
                    return;
                }
                initTable1();
            }
        };
    }();

    jQuery(document).ready(function () {
        TableDatatablesManaged.init();

        $("#cari").on("click",function(){
            $(this).button('loading');
            $('#dataTables').DataTable().ajax.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        })        
    });
</script>
