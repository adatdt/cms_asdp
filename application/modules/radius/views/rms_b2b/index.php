
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .bootstrap-tagsinput  { width:100% !important; }

</style>
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

        <?php $now = date("Y-m-d");
        $last_week = date('Y-m-d', strtotime("- 30 days")) ?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline pad-top">

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal awal dan akhir berlaku</div>
                                                <input type="text" class="form-control  input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>" readonly>

                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal aktif</div>
                                                <input type="text" class="form-control  input-small" id="activeDate"  readonly placeholder="YYYY-MM-DD">

                                            </div>                                            


                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <?php echo form_dropdown('', $port, '', 'id="port" class="form-control select2"'); ?>
                                            </div>                                            

                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode RMS
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>    
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode RMS','rmsCode')">Kode RMS</a>
                                                        </li>
                                                        <!-- <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Longitude','longitude')">Longitude</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Latitude','latitude')">Latitude</a>
                                                        </li>                                                                                                                          -->
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="rmsCode" name="searchData" id="searchData"> 
                                            </div>                          

                                            <div class="input-group select2-bootstrap-prepend pad-top">

                                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                                    <span class="ladda-label">Cari</span>
                                                    <span class="ladda-spinner"></span>
                                                </button>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                    <p></p>

                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th></th>
                                <th>NO</th>
                                <th>KODE RMS</th>
                                <th>PELABUHAN</th>
                                <th>TANGGAL<BR> AKTIF</th>
                                <th>TANGGAL<BR> MULAI</th>
                                <th>TANGGAL<BR> AKHIR</th>
                                <th>LATITUDE</th>
                                <th>LONGITUDE</th>
                                <th>JARAK RADIUS</th>
                                <th>CHANNEL</th>
                                <th>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    LAYANAN
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
                                <th>DIBUAT<BR> OLEH</th>
                                <th>TANGGAL <BR> DIBUAT</th>
                                <th>VIEW MAP</th>
                                <th>KETERANGAN</th>
                                <th>STATUS</th>
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
<?php include "modal_maps.php" ?>
<?php include "fileJs.php"; ?>
<?php include "jsMap.php"; ?>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script type="text/javascript">
    const myData = new MyData();    
    
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });    

    var table= {
        loadData: function() {
            const getTable = $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms_b2b') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.port = document.getElementById('port').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.activeDate = document.getElementById('activeDate').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {
                            "className":      'details-control',
                            "orderable":      false,
                            "data":           null,
                            "defaultContent": '<span  class="label label-success"><i class="fa fa-plus" aria-hidden="true"></i></span>',
                            "targets": 0
                        },                    
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "rms_code", "orderable": false, "className": "text-left"},
                        {"data": "port_name", "orderable": false, "className": "text-left"},
                        {"data": "reservation_date", "orderable": false, "className": "text-left"},
                        {"data": "start_date", "orderable": false, "className": "text-left"},
                        {"data": "end_date", "orderable": false, "className": "text-left"},
                        {"data": "latitude", "orderable": false, "className": "text-left"},
                        {"data": "longitude", "orderable": false, "className": "text-left"},
                        {"data": "radius", "orderable": false, "className": "text-center"},
                        {"data": "channel", "orderable": false, "className": "text-left"},
                        {"data": "layanan", "orderable": false, "className": "text-left"},
                        {"data": "created_by", "orderable": false, "className": "text-left"},
                        {"data": "created_on", "orderable": false, "className": "text-left"},
                        {"data": "view_map", "orderable": false, "className": "text-left"},
                        {"data": "ket", "orderable": false, "className": "text-center"},
                        {"data": "status", "orderable": false, "className": "text-center"},
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
                searching:false,
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

            $('#dataTables tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var td = $(this).closest('td');

                var row = getTable.row( tr );

        
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    td.html('<span  class="label label-success"><i class="fa fa-plus" aria-hidden="true"></i></span>');
                }
                else {
                    // Open this row
                    row.child( myData.format(row.data()) ).show(1000);
                    tr.addClass('shown');  
                    td.html('<span  class="label label-danger"><i class="fa fa-minus" aria-hidden="true"></i></span>');  

                    myData.detailOutlet(row.data())  
                    $(`#add_detail_web_${row.data().id}`).on("click", function(){
                        $(`#detail_code_rms`).val(row.data().rms_code_enc)     
                        $(`#code_rms`).val(row.data().rms_code)     
                        // $("#getEmail").tagsinput('removeAll');
                    })

                    myData.detailOutletExcept(row.data())  
                    myData.detailMerchant(row.data())                      
                    myData.detailGolongan(row.data())  

                }                
                
            } );      
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

        $("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });   

        $('#activeDate').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });        
        
        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
            // startDate: new Date()
            startDate: `<?php echo $last_week; ?>`
        });


        $("#dateFrom").change(function() {

            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth() + 1);
            someDate.getFullYear();
            let endDate = myData.formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo').datepicker('remove');

            // Re-int with new options
            $('#dateTo').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datepicker("update")
        });        


        
    });

</script>

