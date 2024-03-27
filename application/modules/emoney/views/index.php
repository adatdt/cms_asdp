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
                    <?php echo $title; ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Booking </div>
                        <input class="form-control input-small date" id="booking_date" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>

                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Pembayaran</div>
                        <input class="form-control input-small date" id="payment_date" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <button type="button" class="btn btn-info" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">cari</button>
                </div>
            
                <br />
                
                <table class="table table-bordered table-hover" id="tblemoney">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Kode <br />Booking</th>
							<th>Tanggal Booking</th>
                        <!--
                            <th>Tanggal Booking</th>
                        
                            <th>Nomer Invoice</th>
                        -->
                            <th>Total (Rp.)</th>
                            <th>Jenis Pembayaran</th>
                            <th>Tipe</th>
							<th>Tanggal <br />Pembayaran</th>
                        </tr>
                    </thead>
                    <tfood></tfood>
                </table>
            </div>
        </div>
        <!-- end: Gate In: Summary -->
        
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/highcharts/js/highcharts.js') ?>"></script>
<script type="text/javascript">

var emoney= {

	loadData: function() {
		$('#tblemoney').DataTable({
			"ajax": {
                "url": "<?php echo site_url('emoney') ?>",
                "type": "POST",
                "data": function(d) {
                    d.payment_date = document.getElementById('payment_date').value;
                    d.booking_date = document.getElementById('booking_date').value;
                   // d.dateFrom = document.getElementById('dateFrom').value;
                   // d.dateTo = document.getElementById('dateTo').value;
                },
            },
			
            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "booking_code", "orderable": true},
				{"data": "booking_date", "orderable": true},
               // {"data": "invoice_number", "orderable": true},
				{"data": "amount", "orderable": true},
                {"data": "name_method", "orderable": true},
                {"data": "type", "orderable": true, "className": "text-center"},
				{"data": "payment_date", "orderable": true},
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
            "order": [[ 6, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblemoney').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
		});

        $('#export_tools > li > a.tool-action').on('click', function() {
            var data_tables = $('#tblemoney').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblemoney').DataTable().ajax.reload();
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
	emoney.init();

	$('.date').datepicker({
		format: 'yyyy-mm-dd',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,
		
	}).on('changeDate',function(e) {
		//emoney.reload();
	});

    $("#cari").click(function(){
        $(this).button('loading');
        emoney.reload();
        $('#tblemoney').on('draw.dt', function() {
            $("#cari").button('reset');
        });
    });
});
</script>