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
                    <!-- <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div> -->
                    <div class="pull-right btn-add-padding"> <?php if($download_excel){?>

                        <button class="btn btn-sm btn-warning" id="btndownload">Excel</button>
                        
                    <?php } ?></div>

                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tanggal Schedule</div>
                                                <input type="text" name="dateFrom" id="dateFrom" class="form-control input-small date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" name="dateTo" id="dateTo" class="form-control input-small date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $now; ?>" readonly>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">

                                                    <?php if ($row_port!=0){} else { ?>
                                                    <option value="">Pilih</option>
                                                    <?php }  foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }  ?>
                                                </select>
                                            </div> 

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Dermaga</div>
                                                <select id="dock" class="form-control js-data-example-ajax select2 input-small" dir="" name="dock">
                                                    <?php if(!empty($row_port!=0)) { ?>
                                                        <option value="">Pilih</option>
                                                        <?php foreach($dock as $key=>$value ) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?></option>
                                                        <?php  } ?>
                                                    <?php } else { ?>
                                                    <option value="">Pilih</option>
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
                                            <th>KODE JADWAL</th>
                                            <th>NAMA KAPAL</th>
                                            <th>TANGGAL JADWAL</th>
                                            <th>PELABUHAN</th>
                                            <th>DERMAGA</th>
                                            <th>TUJUAN</th>
                                            <th>JAM MASUK <br>ALUR</th>
                                            <th>JAM SANDAR<br></th>
                                            <th>JAM BUKA <br>LAYANAN</th>
                                            <th>JAM TUTUP <br>LAYANAN</th>
                                            <th>JAM TUTUP <br>RAMDOR</th>
                                            <th>JAM BERANGKAT</th>
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

function get_data()
{
    $.ajax({
        url:"<?php echo site_url()?>transaction/trx_schedule/get_data",
        data:"port="+$("#port").val(),
        type:"post",
        dataType:"json",
        success:function(x)
        {
            var html="<option value=''>Pilih</option>";

            for(var i=0;i<x.length;i++)
            {
                html +="<option value='"+x[i].id+"'>"+x[i].name+"</option>";
            }

            $("#dock").html(html);
            // console.log(x)

        }
    });
} 

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/trx_schedule') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dock = document.getElementById('dock').value;
                    d.port = document.getElementById('port').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.dateTo = document.getElementById('dateTo').value;
                },
            },


         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "schedule_code", "orderable": true, "className": "text-left"},
                    {"data": "ship_name", "orderable": true, "className": "text-left"},
                    {"data": "schedule_date", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "dock_name", "orderable": true, "className": "text-left"},
                    {"data": "port_destination", "orderable": true, "className": "text-left"},
                    {"data": "ploting_date", "orderable": true, "className": "text-left"},
                    {"data": "docking_date", "orderable": true, "className": "text-left"},
                    {"data": "open_boarding_date", "orderable": true, "className": "text-left"},
                    {"data": "close_boarding_date", "orderable": true, "className": "text-left"},
                    {"data": "close_ramp_door_date", "orderable": true, "className": "text-left"},
                    {"data": "sail_date", "orderable": true, "className": "text-left"},
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
            "order": [[ 2, "desc" ]],
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

    
    $(document).ready(function () {
        table.init();

        $("#btndownload").click(function(event){

            var sortFrom= $('#dateFrom').val();
            var sortTo= $('#dateTo').val();
            var port= $('#port').val();
            var dock= $('#dock').val();
            var search= $('.dataTables_filter input').val();

            window.location.href="<?php echo site_url('transaction/trx_schedule/download?') ?>sortFrom="+sortFrom+"&sortTo="+sortTo+"&port="+port+"&dock="+dock+"&search="+search;
        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $("#port").on("change",function(){
            table.reload();
            get_data();        
        });

        $("#dock").on("change",function(){
            table.reload();  
        });
        
        $("#dateFrom").on("change",function(){
            table.reload();
        });

        $("#dateTo").on("change",function(){
            table.reload();
        });
    });
</script>
