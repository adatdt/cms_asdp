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
                    <div class="pull-right btn-add-padding" style="padding-left: 5px"><?php echo $btn_add; ?></div>
                    <div class="pull-right btn-add-padding" style="padding-left: 5px"><?php echo $btn_excel; ?></div>
                    <div class="pull-right btn-add-padding"> <?php if($import){?>

                        <a href="<?php echo base_url()?>template_excel/import_excel_schedule.xlsx" class="btn btn-sm btn-warning">Format Excel</a>
                        
                    <?php } ?></div>
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">


                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                        <option value="">Pilih</option>
                                        <?php foreach($port as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div> 

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Deramaga</div>
                                    <select id="dock" class="form-control js-data-example-ajax select2 input-small" dir="" name="dock">
                                        <option value="">Pilih</option>
                                    </select>
                                </div>   

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon ">Tanggal</div>
                                    <input type="text" class="form-control date input-small" name="dateFrom" placeholder="YYY-MM-DD" value="<?php echo $last_week ?>" id="dateFrom">
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control date input-small" name="dateTo" placeholder="YYY-MM-DD" value="<?php echo $now ?>" id="dateTo">
                                    <div class="input-group-addon "><i class="icon-calendar"></i></div>
                                    
                                </div> 

                            </div>
                            <div class="col-sm-12 form-inline"></div>
                            <div class="col-sm-12 form-inline">
                                
                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">&nbsp;&nbsp;&nbsp;&nbsp;Kapal&nbsp;&nbsp;&nbsp;&nbsp;</div>
                                    <select id="ship" class="form-control js-data-example-ajax select2 input-small" dir="" name="ship">
                                        <option value="">Pilih</option>
                                        <?php foreach($ship as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tipe Kapal</div>
                                    <select id="class" class="form-control js-data-example-ajax select2 input-small" dir="" name="class">
                                        <option value="">Pilih</option>
                                        <?php foreach( $ship_class as $key=>$value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id)?>"><?php echo strtoupper($value->name) ?></option>
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
                                <th>TANGGAL JADWAL</th>
                                <th>KODE JADWAL</th>
                                <th>NAMA KAPAL</th>
                                <th>TIPE KAPAL</th>
                                <th>PELABUHAN</th>
                                <th>DERMAGA</th>
                                <th>JUMLAH <br>TRIP</th>
                                <th>JAM <br>SANDAR</th>
                                <th>JAM BUKA <br>BOARDING</th>
                                <th>JAM TUTUP <br>BOARDING</th>
                                <th>JAM TUTUP <br>RAMDOR</th>
                                <th>JAM <br>BERANGKAT</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AKSI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
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

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('pelabuhan/schedule') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dock = document.getElementById('dock').value;
                    d.port = document.getElementById('port').value;
                    d.class = document.getElementById('class').value;
                    d.ship = document.getElementById('ship').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                },
            },
         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "schedule_date", "orderable": true, "className": "text-left"},
                    {"data": "schedule_code", "orderable": true, "className": "text-left"},
                    {"data": "ship_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_class", "orderable": true, "className": "text-left"},

                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "dock_name", "orderable": true, "className": "text-left"},
                    {"data": "trip", "orderable": true, "className": "text-left"},
                    {"data": "docking_on", "orderable": true, "className": "text-left"},
                    {"data": "open_boarding_on", "orderable": true, "className": "text-left"},
                    {"data": "close_boarding_on", "orderable": true, "className": "text-left"},
                    {"data": "close_rampdoor_on", "orderable": true, "className": "text-left"},
                    {"data": "sail_time", "orderable": true, "className": "text-left"},
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

function get_dock()
{
    $.ajax({
        type:"post",
        url:"<?php echo site_url()?>pelabuhan/schedule/get_dock",
        data: 'port='+$('#port').val(),
        dataType :"json",
        success:function(x){

            var html="<option value=''>Pilih</option>";

            for(var i=0; i<x.length; i++)
            {
                html +="<option value='"+x[i].id+"'>"+x[i].name+"</option>";                   
            }

            $("#dock").html(html);
            // console.log(html);
        }
    });
}


jQuery(document).ready(function () {
    table.init();

    $("#port").on("change",function(){
        table.reload();
    });

    $('.date').datepicker({
        format: 'yyyy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        // endDate: new Date(),
    });


    $("#port").on("change",function(){
        get_dock();
        table.reload();
    });

    $("#dock").on("change",function(){
        table.reload();
    });

    $("#class").on("change",function(){
        table.reload();
    });

    $("#ship").on("change",function(){
        table.reload();
    });

    $("#dateTo").on("change",function(){
        table.reload();
    });

    $("#dateFrom").on("change",function(){
        table.reload();
    });

});
</script>
