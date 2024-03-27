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
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">

                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tanggal Pembayaran</div>
                                    <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" placeholder="YYYY-MM-DD">
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" placeholder="YYYY-MM-DD">
<!--                                                 <div class="input-group-addon "><i class="icon-calendar"></i></div> -->

                                </div>    

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tanggal Invoice</div>
                                    <input type="text" class="form-control date input-small" autocomplete="off" id="due_date" placeholder="YYYY-MM-DD">

                                </div> 

                            </div>

                        </div>
                    </div>
                    <div class="row">

                        <!-- looping jumlah dermaga -->
                        <div class="col col-md-4 form-group">
                            <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption" align="center">Dermaga 4 </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                    <table class="table table-striped">

                                        <!-- looping jumlah kapal per dermaga -->
                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                    </table>
                                    </div>

                                </div>
                            </div>  
                        </div>

                        <div class="col col-md-4 form-group">
                            <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption" align="center">Dermaga 4 </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                    <table class="table table-striped">

                                        <!-- looping jumlah kapal per dermaga -->
                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                    </table>
                                    </div>

                                </div>
                            </div>  
                        </div>

                        <div class="col col-md-4 form-group">
                            <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption" align="center">Dermaga 4 </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                    <table class="table table-striped">

                                        <!-- looping jumlah kapal per dermaga -->
                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                    </table>
                                    </div>

                                </div>
                            </div>  
                        </div>

                        <div class="col col-md-4 form-group">
                            <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption" align="center">Dermaga 4 </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                    <table class="table table-striped">

                                        <!-- looping jumlah kapal per dermaga -->
                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

                                        <tr  align="center">
                                            <td>
                                                <a onclick="showModal('<?php echo site_url("transaction/stc/edit/14") ?>')" title="Edit">Ayunda kapal</a> 
                                            </td>
                                        </tr>

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
</div>

<script type="text/javascript">

// var table= {
//     loadData: function() {
//         $('#dataTables').DataTable({
//             "ajax": {
//                 "url": "<?php echo site_url('transaction/opening_balance') ?>",
//                 "type": "POST",
//                 "data": function(d) {
//                     // d.port = document.getElementById('port').value;
//                     // d.team = document.getElementById('team').value;
//                 },
//             },

//             "serverSide": true,
//             "processing": true,
//             "columns": [
//                     {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
//                     {"data": "trx_date", "orderable": true, "className": "text-left"},
//                     {"data": "username", "orderable": true, "className": "text-left"},
//                     {"data": "full_name", "orderable": true, "className": "text-left"},
//                     {"data": "shift_name", "orderable": true, "className": "text-center"},
//                     {"data": "port_name", "orderable": true, "className": "text-left"},
//                     {"data": "code", "orderable": true, "className": "text-left"},
//                     {"data": "total_cash", "orderable": true, "className": "text-center"},
//                     {"data": "actions", "orderable": false, "className": "text-center"},
//             ],
//             "language": {
//                 "aria": {
//                     "sortAscending": ": activate to sort column ascending",
//                     "sortDescending": ": activate to sort column descending"
//                 },
//                   "processing": "Proses.....",
//                   "emptyTable": "Tidak ada data",
//                   "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
//                   "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
//                   "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
//                   "lengthMenu": "Menampilkan _MENU_",
//                   "search": "Pencarian :",
//                   "zeroRecords": "Tidak ditemukan data yang sesuai",
//                   "paginate": {
//                     "previous": "Sebelumnya",
//                     "next": "Selanjutnya",
//                     "last": "Terakhir",
//                     "first": "Pertama"
//                 }
//             },
//             "lengthMenu": [
//                 [10, 25, 50, 100],
//                 [10, 25, 50, 100]
//             ],
//             "pageLength": 10,
//             "pagingType": "bootstrap_full_number",
//             "order": [[ 0, "desc" ]],
//             "initComplete": function () {
//                 var $searchInput = $('div.dataTables_filter input');
//                 var data_tables = $('#dataTables').DataTable();
//                 $searchInput.unbind();
//                 $searchInput.bind('keyup', function (e) {
//                     if (e.keyCode == 13 || e.whiche == 13) {
//                         data_tables.search(this.value).draw();
//                     }
//                 });
//             },
//         });

//         $('#export_tools > li > a.tool-action').on('click', function() {
//             var data_tables = $('#dataTables').DataTable();
//             var action = $(this).attr('data-action');

//             data_tables.button(action).trigger();
//         });
//     },

//     reload: function() {
//         $('#dataTables').DataTable().ajax.reload();
//     },

//     init: function() {
//         if (!jQuery().DataTable) {
//             return;
//         }

//         this.loadData();
//     }
// };


    
    jQuery(document).ready(function () {
        // table.init();

        // $("#port").on("change",function(){
        //     table.reload();
        // });

        // $("#team").on("change",function(){
        //     table.reload();
        // });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });


        
    });

</script>
