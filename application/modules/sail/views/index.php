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
                
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Keberangkatan</div>
                        <input class="form-control input-small date" id="date" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <button type="button " class="btn btn-info" id="cari" >cari</button>
                </div>		
                
                <br />
                <table class="table table-bordered table-hover" id="tblsail">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
							<th>Jam Keberangkatan</th>
                            <th>Nama Kapal</th>
                            <th>Tanggal Keberangkatan</th>
							<th>Status</th>
							<th>Action</th>
                        </tr>
                    </thead>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
        <!-- end: Gate In: Summary -->
        
    </div>
</div>
<link rel="stylesheet" href="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.css'); ?>">
<script src="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/highcharts/js/highcharts.js') ?>"></script>
<script type="text/javascript">

var sail= {

	loadData: function() {
		$('#tblsail').DataTable({
			"ajax": {
                "url": "<?php echo site_url('sail') ?>",
                "type": "POST",
                "data": function(d) {
                    d.departdate = document.getElementById('date').value;
                   // d.dateFrom = document.getElementById('dateFrom').value;
                   // d.dateTo = document.getElementById('dateTo').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
				{"data": "departure", "orderable": true},
				{"data": "ship_name", "orderable": true},
				{"data": "depart_date", "orderable": true},
				{"data": "status", "orderable": false},
				{"data": "action", "orderable": false},


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
            "order": [[ 3, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblsail').DataTable();
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
            var data_tables = $('#tblsail').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblsail').DataTable().ajax.reload();
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
	sail.init();
	
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

    setTimeout(function() 
    {
      $('.menu-toggler').trigger('click');
    }, 0);

	$('.date').datepicker({
		format: 'yyyy-mm-dd',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,
		
	}).on('changeDate',function(e) {
		//sail.reload();
	});

    $("#cari").on("click",function(){

        sail.reload();
    });
});
</script>