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
                <!--
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal open</div>
                        <input class="form-control input-small date" id="datefrom" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>

                    <div class="input-group">
                        <div class="input-group-addon">Sampai close</div>
                        <input class="form-control input-small date" id="dateto" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <button class="btn btn-info" id='cari' >Cari</button>
                </div>
            -->
			    <br />

			    <table class="table table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th class="text-center">No.</th>
								<th>Nama kapal</th>
								<th>Tanggal Sandar</th>
                                <th>Tanggal <br>Open Boarding</th>
                                <th>Tanggal <br>Close Boarding</th>
                                <th>Tanggal <br>Berangkat</th>
                                <th>Nama <br> Pelabuhan</th>
                                <th>Dermaga</th>
                                <th>Status</th>
                                <th>Aksi</th>
							</tr>
						</thead>

						<tfood></tfood>
				</table>
			</div>				
        </div>
    </div>
        <!-- end: Gate In: Summary -->
        
</div>

<link rel="stylesheet" href="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.css'); ?>">
<script src="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/highcharts/js/highcharts.js') ?>"></script>
<script type="text/javascript">

var sandar= {

	loadData: function() {
		$('#dataTables').DataTable({
            
			"ajax": {
                "url": "<?php echo site_url('open_boarding') ?>",
                "type": "POST",
                "data": function(d) {
                  // d.departdate = document.getElementById('departdate').value;
                //  d.datefrom = document.getElementById('datefrom').value;
                //  d.dateto = document.getElementById('dateto').value;
                },
            },
            
            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "ship_name", "orderable": true},
				{"data": "created_on", "orderable": true},
                {"data": "open_boarding_date", "orderable": true},
                {"data": "close_boarding_date", "orderable": true},
                {"data": "depart_date", "orderable": true},
				{"data": "port_name", "orderable": true},
				{"data": "dock_name", "orderable": true},
                {"data": "status", "orderable": false},
                {"data": "action", "orderable": false},

            ],
            
            
           
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",

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
	// Chart.init();
	sandar.init();

    <?php if ($this->session->flashdata('status')) { ?>
        $.notific8('<?php echo $this->session->flashdata('message');  ?>', {
        life: 5000,
        heading: '<?php echo $this->session->flashdata('status');  ?>',
        // theme: 'amethyst',
        // sticky: true,
        horizontalEdge: 'bottom',
        verticalEdge: 'right',
        zindex: 1500
      });
<?php } ?>

	$('.date').datepicker({
       // minDate:moment().add(52,'minutes'),
		format: 'yyyy-mm-dd',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,

		
	}).on('changeDate',function(e) {
		//sandar.reload();
	});
    

    $("#cari").on("click",function(){
        sandar.reload();
    });

    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);
});
</script>