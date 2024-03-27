<!-- start modal -->

<div id="myModal" class="modal fade" role="dialog" >

	<div class="modal-dialog" style="width:1000px">
		<div class="modal-content">
			<div class="modal-header" align="left">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				Ini adalah 
			</div>
			<div class="modal-body">
				ini adalah modal body
			</div>
			<div class="modal-footer">
				ini Adalah footernya
			</div>
			
		</div>
	</div>
</div>
<!-- end modal -->

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
        <br />
        <div class="portlet box blue-madison">
            <div class="portlet-title">
                <div class="caption">
                    <h4><?php echo $title; ?></h4>
                </div>
                <div class="tools">
                    <div class="pull-right">
                        <?php echo generate_button('port', 'add', '<a href="' . site_url('port/add') . '" class="btn btn-warning">Tambah Data</a>'); ?>
						
						<!-- <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal" >Add Data</button> -->
                    </div>
                </div>
            </div>
            <div class="portlet-body">
				<div class="form-inline">
					<div class="input-group">
						<div class="input-group-addon">Tanggal Keberangkatan</div>
							<input class="form-control input-small date" id="dateFrom" placeholder="DD-MMM-YYYY">
						<div class="input-group-addon"><i class="icon-calendar"></i></div>
					</div>
					<a class="btn btn-success btn-md" >Cari</a>
				</div>
				<br />
                <table class="table table-bordered table-hover" id="dataTables">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
							<th>Service</th>
                            <th>Kode </th>
							<th>Tujuan</th>
							<th>Keberangkatan</th>
							<th>Jam</th>
							<th>Tanggal<br /> Berangkat</th>
							<th >Action</th>
                        </tr>
                    </thead>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.css'); ?>">
<script src="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.js'); ?>"></script>
<script type="text/javascript">
    var TableDatatablesManaged = function () {

        var initTable1 = function () {

            var table = $('#dataTables');

						
            // begin first table
            table.dataTable({
                "ajax": {
                    "url": "<?php echo site_url('booking') ?>",
                    "type": "POST",
                    "data": function (d) {
                    },
                },
                "serverSide": true,
                "processing": true,
				//"order":[[0,'desc']],
                "columns": [
                    {"data": "number", "orderable": false, "className": "text-center", "width": 50},
                    {"data": "customer_name", "orderable": true, "className": "text-left", "width": 80},
					{"data": "service", "orderable": true, "className": "text-left", "width": 80},
                    {"data": "code", "orderable": true, "className": "text-left"},
					{"data": "origin_port_name", "orderable": true, "className": "text-left"},
					{"data": "destination_port_name", "orderable": true, "className": "text-left"},
					{"data": "departure", "orderable": true, "className": "text-left"},
					{"data": "depart_date", "orderable": true, "className": "text-left"},
                    {"data": "actions", "orderable": false, "className": "text-left", "width": 120}
                ],

                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
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

                // Or you can use remote translation file
                //"language": {
                //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
                //},

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                "lengthMenu": [
                    [10, 15, 25, -1],
                    [10, 15, 25, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                                {
                         // "targets": [1,2, 3,4,5,6],
						  "targets": [1,2,3],
                        render: $.fn.dataTable.render.text()
                      }
                ],
                "order": [
                    [0, "desc"]
                ], // set first column as a default sort by asc

                // users keypress on search data
                "initComplete": function () {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });

        }

        return {

            //main function to initiate the module
            init: function () {
                if (!jQuery().dataTable) {
                    return;
                }

                initTable1();
            }

        };

    }();

//if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function () {
        TableDatatablesManaged.init();
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
		format: 'dd-M-yyyy',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,
		
	}).datepicker("setDate", new Date()).on('changeDate',function(e) {
		GateIn.reload();
	});
    $("#dateFrom").datepicker({
            rtl: App.isRTL(),
            autoclose: true,
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#dateTo').datepicker('setStartDate', startDate);
        }).on('clearDate', function (selected) {
            $('#dateTo').datepicker('setStartDate', null);
        });

        $("#dateTo").datepicker({
            rtl: App.isRTL(),
            autoclose: true,
        }).on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#dateFrom').datepicker('setEndDate', endDate);
        }).on('clearDate', function (selected) {
            $('#dateFrom').datepicker('setEndDate', null);
        });
		
    });
//}
</script>
