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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days")); $last_day=date('Y-m-d',strtotime("-1 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                    <div class="pull-right btn-add-padding">
<!--                         <?php if ($btn_excel) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <?php } ?> -->
                    </div>
                </div>
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                    <div class="portlet-body">
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
                                            <a class="label label-primary " data-toggle="tab" href="#tab1">Kontrak Corporate</a>
                                    </li>
                                    <li class="nav-item">
                                            <a class="label label-primary " data-toggle="tab" href="#tab2">Cabang Corporate</a>
                                    </li>
                                </ul>
              
                                <div class="tab-content " >

                                    <div class="tab-pane active" id="tab1" role="tabpanel" >

                                        <table class="table table-bordered table-striped   table-hover" id="dataTables">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>NOMOR KONTRAK</th>
                                                    <th>NAMA CORPORATE</th>
                                                    <th>KODE CORPORATE</th>
                                                    <th>AWAL KONTRAK</th>
                                                    <th>AKHIR KONTRAK</th>
                                                    <th>STATUS AKTIF</th>
                                                    <th>FILE</th>
                                                    <th>URUTAN KONTRAK</th>
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

                                    <div class="tab-pane" id="tab2" role="tabpanel">

                                        <table class="table table-bordered table-striped   table-hover" id="dataTables2">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>KODE CORPORATE</th>
                                                    <th>NAMA CORPORATE</th>
                                                    <th>KODE CABANG</th>
                                                    <th>CABANG CORPORATE</th>
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
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    var table= {
        loadData: function() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('ifcs/corporate/get_detail_contract') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.corporate_code="<?php echo $corporate_code ?>"
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "agreement_number", "orderable": true, "className": "text-left"},
                        {"data": "corporate_name", "orderable": true, "className": "text-left"},
                        {"data": "corporate_code", "orderable": true, "className": "text-left"},
                        {"data": "start_date", "orderable": true, "className": "text-left"},
                        {"data": "end_date", "orderable": true, "className": "text-left"},
                        {"data": "is_active", "orderable": true, "className": "text-left"},
                        {"data": "file_pdf", "orderable": true, "className": "text-center"},
                        {"data": "order_number", "orderable": true, "className": "text-center"},
                        {"data": "actions", "orderable": true, "className": "text-center"},
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

                fnDrawCallback: function(allRow)
                {
                    //console.log(allRow);
                    if(allRow.json.recordsTotal)
                    {
                        $('#download').prop('disabled',false);
                    }
                    else
                    {
                        $('#download').prop('disabled',true);
                    }
                }
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

    var table2= {
        loadData: function() {
            $('#dataTables2').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('ifcs/corporate/get_branch') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.corporate_code="<?php echo $corporate_code ?>"
                    },
                },

             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "corporate_code", "orderable": true, "className": "text-left"},
                        {"data": "corporate_name", "orderable": true, "className": "text-left"},
                        {"data": "branch_code", "orderable": true, "className": "text-left"},
                        {"data": "description", "orderable": true, "className": "text-left"},
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

                fnDrawCallback: function(allRow)
                {
                    //console.log(allRow);
                    if(allRow.json.recordsTotal)
                    {
                        $('#download').prop('disabled',false);
                    }
                    else
                    {
                        $('#download').prop('disabled',true);
                    }
                }
            });

            $('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables2').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
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

    function confirmationAction2(message,url){
        alertify.confirm(message, function (e) {
            if(e){
                returnConfirmation2(url)
            }
        });
    }    

    function returnConfirmation2(url){
        $.ajax({
            url         : url,
            type        : 'GET',
            dataType    : 'json',

            beforeSend: function(){
                $.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});
            },

            success: function(json) {
                if(json.code == 1){
                    toastr.success(json.message, 'Sukses');
                    $('#dataTables').DataTable().ajax.reload(null, false );
                    $('#dataTables2').DataTable().ajax.reload(null, false );
                }else{
                    toastr.error(json.message, 'Gagal');
                }
            },

            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            },

            complete: function(){
                $.unblockUI();
            }
        });
    }




        $(document).ready(function () {
        table.init();
        table2.init();


        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $("#dateTo").change(function(){
            table.reload();
        });

        $("#dateFrom").change(function(){
            table.reload();
        });
        
    });
</script>
