<style>
#center-tbl{
    background:#fff;
}
#center-tbl:hover{
    background:#fff;
    background-color:#fff;
}
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

        <?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"> 
                        <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        

                                        <div class="col-md-5" style="padding-right: 0px;">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Nomor Identitas</span>
                                                    <input id="no_identitas" type="number" class="form-control" placeholder="KTP / SIM / Passport / Kartu Pelajar" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-md-5" style="padding-right: 0px;">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Tanggal</span>
                                                    <input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo $date_p ?>" readonly="readonly">
                                                    <div class="input-group-addon">s/d</div>
                                                    <input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo $date_p ?>" readonly="readonly">
                                                </div>
                                            </div>
                                        </div> -->

                                        <div class="col-md-2" style="padding-left: 5px;">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-danger" id="searching" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                                <?php //echo $tampil?>

                                <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="">
                                    <!-- <?php echo $table?> -->
                                    <thead>
                                        <tr style="background:#40a9f6; color:#fff;">
                                            <th colspan="2">Data Penumpang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="20%"><b>Nama</b></td>
                                            <td id="pName"></td>
                                        </tr>
                                        <tr>
                                            <td><b>Jumlah Perjalanan</b></td>
                                            <td id="pJmlp"></td>
                                        </tr>
                                        <tr>
                                            <td><b>No. Identitas</b></td>
                                            <td id="pNoI"></td>
                                        </tr>
                                    </tbody>
                                    
                                </table> 


                                </div>

                                <table class="table table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>TANGGAL BOOKING</th>
                                            <th>KODE BOOKING</th>
                                            <th>KEBERANGKATAN</th>
                                            <th>TUJUAN</th>
                                            <th>NOMOR TIKET</th>
                                            <th>SERVICE</th>
                                            <th>NAMA KAPAL</th>
                                            <th>TYPE KAPAL</th>
                                            <th>TANGGAL BOARDING</th>
                                            <th>STATUS</th>
                                            
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>

                                
                            </div>
                        </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                 </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>





<script type="text/javascript">

$(document).ready(function(){

    // $('#datefrom').datetimepicker({
    //     format: 'yyyy-mm-dd hh:ii:ss',
    //     changeMonth: true,
    //     changeYear: true,
    //     autoclose: true,
    //     endDate: new Date(),
    // }).on('changeDate',function(e) {
    //     $('#dateto').datetimepicker('setStartDate', e.date)
    // });

    // $('#dateto').datetimepicker({
    //     format: 'yyyy-mm-dd hh:ii:ss',
    //     changeMonth: true,
    //     changeYear: true,
    //     autoclose: true,
    //     startDate: $('#datefrom').val(),
    //     endDate: new Date(),
    // }).on('changeDate',function(e) {
    //     $('#datefrom').datetimepicker('setEndDate', e.date)
    // });

    $('#searching').click(function(){
        table.reload();
    });

    $('#download_excel').click(function(){
        // alert('oy');
        var id_identitas= $('#no_identitas').val();
        window.location.href="<?php echo site_url("crm/download_excel?id_number=")?>"+id_identitas;
    });

    var listData = function(){
        $.ajax({
            url         : 'crm/get_data',
            data        : {
                // server_id : $('#server').val(),
                // start_date : $('#datefrom').val(),
                // end_date : $('#dateto').val()
            },
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                $('#searching').button('loading');
                // unBlockUiId('.box');
            },

            success: function(json) {
                console.log(json);
                // if(json.length != 0){
                //     $('#pName').html(json[0].name);
                //     $('#pJmlp').html(json[0].jml_perjalanan);
                //     $('#pNoI').html(json[0].id_number);
                // }else{
                //     toastr.error('Nomor Identitas Tidak Tersedia');
                // }

                // includeData(json);
                // statusData(json);
                // list_bar(json,'ltc');
                // list_bar(json,'ctl');
                // //dSend = json.data.post;
            },

            error: function() {
                toastr.error('Please contact the administrator');
            },

            complete: function(json){
                $("#searching").button('reset');
                //$('.box').unblock();
            }
        });

    }

    // listData();
    

    var table = {

        loadData : function(){
            $('#dataTables').dataTable({
                "ajax" : {
                    "url" : "crm/get_data",
                    "type" : "post",
                    "data" : function(d){
                        d.id_number = document.getElementById('no_identitas').value;
                    },
                    beforeSend: function(){
                        $('#searching').button('loading');
                        // unBlockUiId('.box');
                    },
                    error: function() {
                        toastr.error('Please contact the administrator');
                    }
                },

                "serverSide" : true,
                "processing" : true,
                // "responsive" : true,
                "columns" : [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "booking_date", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "origin", "orderable": true, "className": "text-left"},
                    {"data": "destination", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "service_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_class", "orderable": true, "className": "text-left"},
                    {"data": "boarding_date", "orderable": true, "className": "text-left"},
                    {"data": "status_name", "orderable": true, "className": "text-left"},
                ],
                "language" : {
                    "aria" : {
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
                "order": [[ 0, "asc" ]],
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
                fnDrawCallback: function(allRow)
                {
                    console.log(allRow.json);

                    if(allRow.json != undefined){
                        var dataProfile = allRow.json.data_s[0];
                        if(dataProfile != undefined || dataProfile != null){         
                            console.log(dataProfile);
                            $('#pName').html(dataProfile.name);
                            $('#pJmlp').html(allRow.json.data_s.jml_perjalanan)
                            $('#pNoI').html(dataProfile.id_number);
                        }else{
                            $('#pName').html('-');
                            $('#pJmlp').html('-');
                            $('#pNoI').html('-');
                        }

                        if(allRow.json.recordsTotal)
                        {
                            $('.download').prop('disabled',false);
                        }
                        else
                        {
                            $('.download').prop('disabled',true);
                        }
                        $("#searching").button('reset');
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

    }


    table.init();

})

</script>



<style type="text/css">
  .padding-title-chart{
    padding: 5px 10px 0px 5px !important;
  }

  .padding-body{
    padding: 0px 5px 0px 20px !important;
  }
</style>
