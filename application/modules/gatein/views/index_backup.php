<div class="page-content-wrapper">
    <div class="page-content">

    	<div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
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

        <br>
        <!-- start: Gate In: Summary -->
        <div class="portlet box blue">
        	<div class="portlet-title">
				<div class="caption">
                    <h4><?php echo $title; ?></h4>
                </div>
				
                <div class="tools">
                	<div class="btn-group pull-right">
					
                    <!--    <button class="btn red dropdown-toggle" data-toggle="dropdown">Export
                            <i class="fa fa-angle-down"></i>
                        </button>
						-->
                        <ul class="dropdown-menu pull-right" id="export_tools">
                            <li>
                                <a href="javascript:;" data-action="0" class="tool-action">
                                    <i class="icon-doc"></i> PDF</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
			
				<!-- start -->
				<ul class="nav nav-tabs">
				  <li class="<?php echo ($tab == 'passanger') ? 'active' : ''; ?>"><a href="#passanger" data-toggle="tab"><span style="font-size:13px width: 50px text-align: center" class="widget-caption ">Gate In Penumpang</span></a></li>
				  <li class="<?php echo ($tab == 'vehicle') ? 'active' : ''; ?>"><a href="#vehicle" data-toggle="tab"><span style="font-size:13px width: 140px text-align: center" class="widget-caption ">Gate In Kendaraan</span></a></li>
				</ul>
				<!-- end -->
				<div class="tab-content">
					<div class="tab-pane <?php echo ($tab == 'passanger') ? 'active' : ''; ?>" id="passanger">	
						 <br />
						 <table class="table table-bordered table-hover" id="tblgatein">
							<thead>
								<tr>
									<th class="text-center">No.</th>
									<th>Kode Booking</th>
									<th>No Tiket</th>
									<th>Nama</th>
									<th>NO KTP</th>
									<th>Tanggal <br />Lahir</th>
									<th>Jenis Kelamin</th>
									<th>Tanggal <br> Gatein</th>
									<th>Tanggal <br> Keberangkatan</th>
								</tr>
							</thead>
							<tfood></tfood>
						</table>

					</div>
					
					<div class="tab-pane <?php echo ($tab == 'vehicle') ? 'active' : ''; ?>" id="vehicle">	
						<br />
						 <table class="table table-bordered table-hover" id="tblgateinvehicle">
							<thead>
								<tr>
									<th class="text-center">No.</th>
									<th>Kode Booking</th>
									<th>No Tiket</th>
									<th>Golongan</th>
									<th>NO Plat</th>
									<th>Tanggal <br> Gatein</th>
									<th>Tanggal <br> Keberangkatan</th>
								</tr>
							</thead>
							<tfood></tfood>
						</table>
					</div>
				</div>
				
            </div>
        </div>
        <!-- end: Gate In: Summary -->
        
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/highcharts/js/highcharts.js') ?>"></script>
<script type="text/javascript">

var gatein= {

	loadData: function() {
		$('#tblgatein').DataTable({
			"ajax": {
                "url": "<?php echo site_url('gatein') ?>",
                "type": "POST",
                "data": function(d) {
                    //d.po = document.getElementById('po').value;
                   // d.dateFrom = document.getElementById('dateFrom').value;
                   // d.dateTo = document.getElementById('dateTo').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "code", "orderable": true},
				{"data": "ticket_number", "orderable": true},
				{"data": "name_checkin", "orderable": true},
				{"data": "id_number", "orderable": true},
                {"data": "birth_date", "orderable": true},
				{"data": "gender", "orderable": true},
				{"data": "gatein_date", "orderable": true},
				{"data": "depart_date", "orderable": true},
            ],
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "processing": "Processing...",
                "emptyTable": "There is no data",
                "info": "Showing _START_ - _END_ of _TOTAL_ data",
                "infoEmpty": "Showing 0 - 0 of 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "lengthMenu": "Show _MENU_",
                "search": "Search :",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "previous": "Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
        	"lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 7, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblgatein').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
            buttons: [
                {
	                extend: 'pdfHtml5',
	                orientation: 'landscape',
	                className: 'btn green btn-outline',
	                exportOptions: {
	                    columns: [ 0, 1, 2, 3, 4, 5 ]
	                },
	                customize : function(doc) {
		                doc.content[1].table.widths = ['10%', '25%', '10%', '25%', '15%', '15%'];
		                //doc.styles.tableHeader.alignment = 'left';
		                for (var i in doc.content[1].table.body) {
		                	doc.content[1].table.body[i][0].alignment = 'center';
		                	doc.content[1].table.body[i][1].alignment = 'left';
		                	doc.content[1].table.body[i][2].alignment = 'left';
		                	doc.content[1].table.body[i][3].alignment = 'left';
		                	doc.content[1].table.body[i][4].alignment = 'left';
		                	doc.content[1].table.body[i][5].alignment = 'left';
		            	}
		            },
	                filename: 'GATE_IN_<?php echo date('Ymd_His') ?>',
	                title: 'Gate In',
	            },
	        ]
		});

        $('#export_tools > li > a.tool-action').on('click', function() {
            var data_tables = $('#tblgatein').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblgatein').DataTable().ajax.reload();
	},

	init: function() {
		if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
	}
};




var gatein2= {

	loadData: function() {
		$('#tblgateinvehicle').DataTable({
			"ajax": {
                "url": "<?php echo site_url('gatein/gateInVehicleList') ?>",
                "type": "POST",
                "data": function(d) {
                    //d.po = document.getElementById('po').value;
                   // d.dateFrom = document.getElementById('dateFrom').value;
                   // d.dateTo = document.getElementById('dateTo').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "code", "orderable": true},
				{"data": "ticket_number", "orderable": true},
				{"data": "vehicle_name", "orderable": true},
				{"data": "id_number", "orderable": true},
				{"data": "gatein_date", "orderable": true},
				{"data": "depart_date", "orderable": true},
            ],
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "processing": "Processing...",
                "emptyTable": "There is no data",
                "info": "Showing _START_ - _END_ of _TOTAL_ data",
                "infoEmpty": "Showing 0 - 0 of 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "lengthMenu": "Show _MENU_",
                "search": "Search :",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "previous": "Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
        	"lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 6, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblgateinvehicle').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
            buttons: [
                {
	                extend: 'pdfHtml5',
	                orientation: 'landscape',
	                className: 'btn green btn-outline',
	                exportOptions: {
	                    columns: [ 0, 1, 2, 3, 4, 5 ]
	                },
	                customize : function(doc) {
		                doc.content[1].table.widths = ['10%', '25%', '10%', '25%', '15%', '15%'];
		                //doc.styles.tableHeader.alignment = 'left';
		                for (var i in doc.content[1].table.body) {
		                	doc.content[1].table.body[i][0].alignment = 'center';
		                	doc.content[1].table.body[i][1].alignment = 'left';
		                	doc.content[1].table.body[i][2].alignment = 'left';
		                	doc.content[1].table.body[i][3].alignment = 'left';
		                	doc.content[1].table.body[i][4].alignment = 'left';
		                	doc.content[1].table.body[i][5].alignment = 'left';
		            	}
		            },
	                filename: 'GATE_IN_<?php echo date('Ymd_His') ?>',
	                title: 'Gate In',
	            },
	        ]
		});

        $('#export_tools > li > a.tool-action').on('click', function() {
            var data_tables = $('#tblgateinvehicle').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblgateinvehicle').DataTable().ajax.reload();
	},

	init: function() {
		if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
	}
};

jQuery(document).ready(function () {
	// Chart.init();
	gatein.init();
	gatein2.init();

	$('.date').datepicker({
		format: 'dd-M-yyyy',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,
		
	}).on('changeDate',function(e) {
		gatein.reload();
	});
});
</script>