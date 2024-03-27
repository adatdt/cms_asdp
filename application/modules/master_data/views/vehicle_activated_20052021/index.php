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
                    <div class="pull-right btn-add-padding" style="padding-left: 10px"><?php echo $btn_add; ?></div>
                   
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                    	<div class="row">
                    		<div class="col-sm-12">
                    			<div class="col col-md-3">
                    				<div class="input-group select2-bootstrap-prepend">
                    					<div class="input-group-addon">PELABUHAN</div>
                    					<select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                    						<option value="">Pilih</option>
                    						<?php foreach($port as $key=>$value ) {?>
                    							<option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                    						<?php } ?>
                    					</select>
                    				</div> 
                    			</div>
                    			<div class="col col-md-3">
                    				<div class="input-group select2-bootstrap-prepend">
                    					<div class="input-group-addon">GOLONGAN</div>
                    					<select id="vehicle_class" class="form-control js-data-example-ajax select2 input-small" dir="" name="vehicle_class">
                    						<option value="">Pilih</option>
                    						<?php foreach($vehicle_class as $key=>$value ) {?>
                    							<option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                    						<?php } ?>
                    					</select>
                    				</div> 
                    			</div>
                    			<div class="col col-md-3">
                    				<div class="input-group select2-bootstrap-prepend">
                    					<div class="input-group-addon">KELAS</div>
                    					<select id="ship_class" class="form-control js-data-example-ajax select2 input-small" dir="" name="ship_class">
                    						<option value="">Pilih</option>
                    						<?php foreach($ship_class as $key=>$value ) {?>
                    							<option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                    						<?php } ?>
                    					</select>
                    				</div> 
                    			</div>
                    		</div>
                    	</div>
                    </div>

                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>PELABUHAN</th>
                                <th>GOLONGAN</th>
                                <th>KELAS LAYANAN</th>
                                <th>POS MOTOR</th>
                                <th>POS KENDARAAN</th>
                                <th>WEB</th>
                                <th>MOBILE</th>
                                <th>B2B</th>
                                <th>IFCS</th>
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
                "url": "<?php echo site_url('master_data/vehicle_activated') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port = document.getElementById('port').value;
                    d.vehicle_class = document.getElementById('vehicle_class').value;
                    d.ship_class = document.getElementById('ship_class').value;
                },
            },
         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "golongan", "orderable": true, "className": "text-left"},
                    {"data": "class", "orderable": true, "className": "text-left"},
                    {"data": "pos_motor_bike", "orderable": true, "className": "text-center"},
                    {"data": "pos_vehicle", "orderable": true, "className": "text-center"},
                    {"data": "web", "orderable": true, "className": "text-center"},
                    {"data": "mobile", "orderable": true, "className": "text-center"},
                    {"data": "b2b", "orderable": true, "className": "text-center"},
                    {"data": "ifcs", "orderable": true, "className": "text-center"},
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


jQuery(document).ready(function () {
    table.init();

    $("#port").on("change",function(){
        table.reload();
    });

    $("#vehicle_class").on("change",function(){
        table.reload();
    });

    $("#ship_class").on("change",function(){
        table.reload();
    });

});
</script>