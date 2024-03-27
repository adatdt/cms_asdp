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
				  <li class="<?php echo ($tab == 'passanger') ? 'active' : ''; ?>"><a href="#passanger" data-toggle="tab"><span style="font-size:13px width: 50px text-align: center" class="widget-caption btn ">Boarding Penumpang</span></a></li>
				 <li class="<?php echo ($tab == 'passanger_vehicle') ? 'active' : ''; ?>"><a href="#passanger_vehicle" data-toggle="tab"><span style="font-size:13px width: 50px text-align: center" class="widget-caption btn ">Boarding Penumpang Kendaraan</span></a></li>
				  <li class="<?php echo ($tab == 'vehicle') ? 'active' : ''; ?>"><a href="#vehicle" data-toggle="tab"><span style="font-size:13px width: 140px text-align: center" class="widget-caption btn ">Boarding Kendaraan </span></a></li>
				</ul>
				<!-- end -->
				<div class="tab-content">
				<div class="tab-pane <?php echo ($tab == 'passanger') ? 'active' : ''; ?>" id="passanger">
				
				<div class="form-inline">
                	<div class="input-group">
                		<div class="input-group-addon">Tanggal Boarding</div>
                		<input class="form-control input-small boardingDate" id="boardingDate1" placeholder="yyyy-mm-dd">
                		<div class="input-group-addon"><i class="icon-calendar"></i></div>
                	</div>
					<div class="input-group">
                		<div class="input-group-addon">Tanggal Keberangakatan</div>
                		<input class="form-control input-small boardingDate" id="departDate1" placeholder="yyyy-mm-dd">
                		<div class="input-group-addon"><i class="icon-calendar"></i></div>
                	</div>
					<button type="button " class="btn btn-info" id="cari_tanggal1" >cari</button>
                </div>
				<br />
				<div align="right">
				Cari : <input type="input" name="caridata1" id="caridata1" />
				</div>					
                <br />
                <table class="table table-bordered table-hover" id="tblboarding">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Kode Booking</th>
							<th>No Tiket</th>
                            <th>Nama</th>
                            <th>No Identitas</th>
                            <th>Tanggal <br />Lahir</th>
                            <th>Jenis Kelamin</th>
							<th>Tanggal <br> Boarding</th>

                        </tr>
                    </thead>
                    <tfoot></tfoot>
                </table>
				</div>
				
				<div class="tab-pane <?php echo ($tab == 'passanger_vehicle') ? 'active' : ''; ?>" id="passanger_vehicle">
				
				<div class="form-inline">
                	<div class="input-group">
                		<div class="input-group-addon">Tanggal Boarding</div>
                		<input class="form-control input-small boardingDate" id="boardingDate3" placeholder="yyyy-mm-dd">
                		<div class="input-group-addon"><i class="icon-calendar"></i></div>
                	</div>
					<div class="input-group">
                		<div class="input-group-addon">Tanggal Keberangakatan</div>
                		<input class="form-control input-small boardingDate" id="departDate3" placeholder="yyyy-mm-dd">
                		<div class="input-group-addon"><i class="icon-calendar"></i></div>
                	</div>
					<button type="button " class="btn btn-info" id="cari_tanggal3" >cari</button>
                </div>
				<br />
				<div align="right">
				Cari : <input type="input" name="caridata3" id="caridata3" />
				</div>										
                <br />
                <table class="table table-bordered table-hover" id='boarding_passanger_vehicle'>
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Kode Booking</th>
							<th>No Tiket</th>
                            <th>Nama</th>
                            <th>No Identitas</th>
                            <th>Tanggal <br />Lahir</th>
                            <th>Jenis Kelamin</th>
							<th>Tanggal <br> Boarding</th>

                        </tr>
                    </thead>
                    <tfoot></tfoot>
                </table>
				</div>
				
				<div class="tab-pane <?php echo ($tab == 'vehicle') ? 'active' : ''; ?>" id="vehicle">
				<div class="form-inline">
                	<div class="input-group">
                		<div class="input-group-addon">Tanggal Boarding</div>
                		<input class="form-control input-small boardingDate" id="boardingDate2" placeholder="yyyy-mm-dd">
                		<div class="input-group-addon"><i class="icon-calendar"></i></div>
                	</div>
					<div class="input-group">
                		<div class="input-group-addon">Tanggal Keberangakatan</div>
                		<input class="form-control input-small boardingDate" id="departDate2" placeholder="yyyy-mm-dd">
                		<div class="input-group-addon"><i class="icon-calendar"></i></div>
                	</div>
					<button type="button " class="btn btn-info" id="cari_tanggal2" >cari</button>
                </div>
				<br />
				<div align="right">
				Cari : <input type="input" name="caridata2" id="caridata2" />
				</div>
				<table class="table table-bordered table-hover" id="tblboarding2">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Kode Booking</th>
							<th>No Tiket</th>
                            <th>Nama</th>
                            <th>NO Polisi</th>
							<th>Tanggal <br> Boarding</th>

                        </tr>
                    </thead>
                    <tfoot></tfoot>
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

var boarding= {

	loadData: function() {
		$('#tblboarding').DataTable({
			"ajax": {
                "url": "<?php echo site_url('boarding') ?>",
                "type": "POST",
                "data": function(d) {
                    d.caridata1 = document.getElementById('caridata1').value;
                    d.boardingDate1= document.getElementById('boardingDate1').value;
                   d.departDate1= document.getElementById('departDate1').value;
                },
            },

            "serverSide": true,
            "processing": true,
			"searching": false,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "code", "orderable": true},
				{"data": "ticket_number", "orderable": true},
				{"data": "name_checkin", "orderable": true},
				{"data": "id_number", "orderable": true},
                {"data": "birth_date", "orderable": true},
				{"data": "gender", "orderable": true},
				{"data": "boarding_date", "orderable": true},
				//{"data": "departure_date", "orderable": true},
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
                var data_tables = $('#tblboarding').DataTable();
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
            var data_tables = $('#tblboarding').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblboarding').DataTable().ajax.reload();
	},

	init: function() {
		if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
	}
};


var boarding2= {

	loadData: function() {
		$('#tblboarding2').DataTable({
			"ajax": {
                "url": "<?php echo site_url('boarding/vehicleList') ?>",
                "type": "POST",
                "data": function(d) {
                    d.caridata2 = document.getElementById('caridata2').value;
                   d.boardingdate2= document.getElementById('boardingDate2').value;
                   d.departdate2= document.getElementById('departDate2').value;
                },
            },


            "serverSide": true,
            "processing": true,
			"searching" : false,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "booking_code", "orderable": true},
				{"data": "ticket_number", "orderable": true},
				{"data": "vehicle_name", "orderable": true},
				{"data": "id_number", "orderable": true},
				{"data": "boarding_date", "orderable": true},
				//{"data": "depart_date", "orderable": true},
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
            "order": [[5, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblboarding2').DataTable();
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
            var data_tables = $('#tblboarding2').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblboarding2').DataTable().ajax.reload();
	},

	init: function() {
		if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
	}
};


var boarding3= {

	loadData: function() {
		$('#boarding_passanger_vehicle').DataTable({
			"ajax": {
                "url": "<?php echo site_url('boarding/passangerVehicleList') ?>",
                "type": "POST",
                "data": function(d) {
                    d.caridata3 = document.getElementById('caridata3').value;
                    d.boardingdate3= document.getElementById('boardingDate3').value;
                    d.departdate3= document.getElementById('departDate3').value;
                },
            },


            "serverSide": true,
            "processing": true,
			"searching" : false,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "code", "orderable": true},
				{"data": "ticket_number", "orderable": true},
				{"data": "name_checkin", "orderable": true},
				{"data": "id_number", "orderable": true},
                {"data": "birth_date", "orderable": true},
				{"data": "gender", "orderable": true},
				{"data": "boarding_date", "orderable": true},
				//{"data": "departure_date", "orderable": true},
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
                var data_tables = $('#boarding_passanger_vehicle').DataTable();
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
            var data_tables = $('#boarding_passanger_vehicle').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#boarding_passanger_vehicle').DataTable().ajax.reload();
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
	boarding.init();
	boarding2.init();
	boarding3.init();

	$('.boardingDate').datepicker({
		format: 'yyyy-mm-dd',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,
	}).on('changeDate',function(e) {});
	
	
	$("#caridata1").on("keypress",function(){
		if(event.which == 13){
			boarding.reload();
		}
	});
	
	$("#caridata3").on("keypress",function(){
		if(event.which == 13){
			boarding3.reload();
		}
	});
	
	$("#caridata2").on("keypress",function(){
		if(event.which == 13){
			boarding2.reload();
		}
	});
	
	$("#cari_tanggal1").on("click", function (){
		boarding.reload();
	});
	
	$("#cari_tanggal2").on("click", function (){
		boarding2.reload();
	});
	
	$("#cari_tanggal3").on("click", function (){
		boarding3.reload();
	});
});
</script>