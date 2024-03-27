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
                    <div class="pull-right btn-add-padding" style="padding-left: 10px"><?php echo $btn_add; ?></div>
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                    	<div class="row">
                    		<div class="col-sm-12">
                    			<div class="col col-md-3">
                    				<div class="input-group select2-bootstrap-prepend">
                    					<div class="input-group-addon">PELABUHAN</div>
                    					<select id="port_id" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_id">
                    						<option value="">Pilih</option>
                    						<?php foreach($port as $key=>$value ) {?>
                    							<option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                    						<?php } ?>
                    					</select>
                    				</div> 
                    			</div>
                    			<div class="col col-md-3">
                    				<div class="input-group select2-bootstrap-prepend">
                    					<div class="input-group-addon">KELAS</div>
                    					<select id="ship_class_id" class="form-control js-data-example-ajax select2 input-small" dir="" name="ship_class_id">
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
                                <th>KELAS LAYANAN</th>
                                <th>TANGGAL MULAI</th>
                                <th>TANGGAL AKHIR</th>
                                <!--
                                <th>POS MOTOR</th>
                                <th>POS KENDARAAN</th>
                                -->
                                <th>WEB</th>                                                
                                <th>MOBILE</th>
                                <th>B2B</th>
                                <th>IFCS</th>
                                <th>WEB CS</th>
                                <!--
                                <th>MPOS MOTOR</th>
                                <th>MPOS KENDARAAN</th>
                                -->
                                <th>MASA BERLAKU</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AKSI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
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

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('master_data/pedestrian_inactivated') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port_id = document.getElementById('port_id').value;
                    d.ship_class_id = document.getElementById('ship_class_id').value;
                },
            },         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center", "width": 5},
                    {"data": "port", "orderable": false, "className": "text-center"},
                    {"data": "class", "orderable": false, "className": "text-center"},
                    {"data": "start_date", "orderable": true, "className": "text-center"},
                    {"data": "end_date", "orderable": true, "className": "text-center"},
                    {"data": "web", "orderable": false, "className": "text-center"},
                    {"data": "mobile", "orderable": false, "className": "text-center"},
                    {"data": "b2b", "orderable": false, "className": "text-left"},
                    {"data": "ifcs", "orderable": false, "className": "text-center"},
                    {"data": "web_cs", "orderable": false, "className": "text-center"},
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

    $("#port_id").on("change",function(){
        table.reload();
    });

    $("#ship_class_id").on("change",function(){
        table.reload();
    });
});
</script>