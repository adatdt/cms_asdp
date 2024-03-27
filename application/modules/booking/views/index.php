<div class="page-content-wrapper">
    <div class="page-content">
    	<div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent . '">' . $parent; ?></a>
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
                    <?php echo $title; ?>
                </div>
            </div>
            <div class="portlet-body">
				<div class="form-inline">
					<div class="input-group">
						<div class="input-group-addon">Tanggal Keberangkatan</div>
							<input class="form-control input-small date" id="dateFrom" placeholder="YYYY-MM-DD">
						<div class="input-group-addon"><i class="icon-calendar"></i></div>
					</div>
                    <button type="button " class="btn btn-info" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">cari</button>
				</div>
                <br />
                
                <table class="table table-bordered table-hover" id="tblbooking">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
							<th>Service</th>
                            <th>Kode Booking </th>
							<th>Tujuan</th>
							<th>Keberangkatan</th>
							<th>Jam</th>
							<th>Tanggal<br /> Berangkat</th>
							<th>Tanggal<br /> Booking</th>
							<th >Action</th>
                        </tr>
                    </thead>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
        <!-- end: Gate In: Summary -->
        
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/highcharts/js/highcharts.js') ?>"></script>
<script type="text/javascript">

var booking= {

	loadData: function() {
		$('#tblbooking').DataTable({
			"ajax": {
                "url": "<?php echo site_url('booking') ?>",
                "type": "POST",
                "data": function(d) {
                   // d.dateFrom = document.getElementById('dateFrom').value;
                   	d.dateFrom =$("#dateFrom").val();
                },
            },
		 
            "serverSide": true,
            "processing": true,
                  "columns": [
                    {"data": "number", "orderable": false, "className": "text-center"},
                    {"data": "customer_name", "orderable": true, "className": "text-left"},
					{"data": "service", "orderable": true, "className": "text-left"},
                    {"data": "code", "orderable": true, "className": "text-left"},
					{"data": "origin_port_name", "orderable": true, "className": "text-left"},
					{"data": "destination_port_name", "orderable": true, "className": "text-left"},
					{"data": "departure", "orderable": true, "className": "text-left"},
					{"data": "depart_date", "orderable": true, "className": "text-left"},
					{"data": "booking_date", "orderable": true, "className": "text-left"},
                    {"data": "actions", "orderable": false, "className": "text-center"}
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
            "order": [[ 7, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblbooking').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
			/*
            buttons: [
                {
	                extend: 'pdfHtml5',
	                orientation: 'landscape',
	                className: 'btn green btn-outline',
	                exportOptions: {
	                    columns: [ 0, 1, 2, 3, 4, 5,6,7 ]
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
			*/
		});

        $('#export_tools > li > a.tool-action').on('click', function() {
            var data_tables = $('#tblbooking').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblbooking').DataTable().ajax.reload();
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
	booking.init();

	$('.date').datepicker({
		format: 'yyyy-mm-dd',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,
		endDate: new Date()
	}).on('changeDate',function(e) {
		//booking.reload();
	});
	
    $("#cari").on("click",function(){
        $(this).button('loading');
        booking.reload();
        $('#tblbooking').on('draw.dt', function() {
            $("#cari").button('reset');
        });
    });

    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);
});
</script>