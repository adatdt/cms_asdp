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
                   <!--  <div class="form-inline">
                        <div class="row">
                            <div class="col-md-12">                                
                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Kategori</div>
                                    <select id="category" class="form-control js-data-example-ajax select2" dir="" name="category">
                                        <option value="">Pilih</option>
                                        <?php foreach($kategori as $key=>$value ) { ?>
                                            <option value="<?php echo $value->category_name; ?>"><?php echo $value->category_name; ?></option>
                                        <?php } ?>
                                        <option value="lainnya">lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                        </div>
                    </div> -->


                    <div class="row">
                        <div class="col-sm-12 form-inline">

                           <div class="input-group pad-top">
                                <div class="input-group-btn">
                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nama Param
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Nama Param','param_name')">Nama Param</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Value Param','param_value')">Value Param</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Tipe Param','type')">Tipe Param</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Tipe Value','value_type')">Tipe Value</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="changeSearch('Info','info')">Info</a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /btn-group -->
                                <input type="text" class="form-control" placeholder="Cari Data" data-name="param_name" name="searchData" id="searchData"> 
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
                                <th>NAMA PARAM</th>
                                <th>VALUE PARAM</th>
                                <th>TIPE PARAM</th>
                                <th>TIPE VALUE</th>
                                <th>INFO</th>
                                <!-- <th>KATEGORI</th> -->
                                <th>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    AKSI
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function replaceStyle(text)
    {

        return btoa(text); // encode ke base64
    }

    function replaceStyle_23052021(text)
    {
        return text.replaceAll('style', 'monkey-1777')
    }

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
                    "url": "<?php echo site_url('master_data/setting_param') ?>",
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
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "param_name", "orderable": true, "className": "text-left"},
                    {"data": "param_value", "orderable": true, "className": "text-center"},
                    {"data": "type", "orderable": true, "className": "text-center"},
                    {"data": "value_type", "orderable": true, "className": "text-center"},
                    {"data": "info", "orderable": true, "className": "text-center"},
                    // {"data": "category_name", "orderable": true, "className": "text-center"},
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
