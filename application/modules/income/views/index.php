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
			</div>
            <div class="portlet-body">

                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Keberangkatan</div>
                        <input class="form-control input-small date" id="date" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>

                    <div class="input-group" >
                        <div class="input-group-addon">Nama kapal</div>
                        <!--
                        <input class="form-control input-small " placeholder="yyyy-mm-dd">
                    -->

                        <select  id="ship_id" class="form-control">
                        <option value="">--pilih--</option>
                        <option value="1">Kapal 1</option>
                        <option value="2">Kapal 3</option> 

                        </select>
                        
                    </div>

                    <button type="button " class="btn btn-info" id="cari" >cari</button>
                    <span id='export_tools'> <a href="javascript:;" data-action="0" class="tool-action btn btn-warning" id="export_tools"><i class="icon-doc"></i> Export</a></span>
                </div>      
                

				<br />
                
                <table class="table table-bordered table-hover" id="tblcheckin">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Nama <br />Kapal</th>
							<th>Jam Keberangkatan</th>
                            <th>Tanggal Keberangkatan</th>
                            <th>Jumlah penumpang</th>
                            <th>Jumlah penumpang Kendaraan</th>
                            <th>Jumlah Kendaraan </th>
                            <th>Jumlah Pendapatan</th>

                        </tr>
                    </thead>

                    <body>
                        <?php $a=10; 
                            $b=1;
                            $c=0;
                            $total=0;
                            for($i=0; $i<=$a;$i++)
                            {

                        ?>
                        <tr>
                            <td class="text-center"><?php echo $b ?></td>
                            <td>kapal 1</td>
                            <td>01:00</td>
                            <td>19 September 2018</td>
                            <td>10</td>
                            <td>3</td>
                            <td>1</td>
                            <td><?php echo $bc=1000+$c; ?></td>
                        </tr>
                    <?php  $b+=1; $c+=10; $total+=$bc;}?>

                    </tbody>
                     <tfoot>
            <tr>
                <th colspan="7" style="text-align:right">Total:</th>
                <th></th>
            </tr>
        </tfoot>

                </table>
            </div>
        </div>
        <!-- end: Gate In: Summary -->
        
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/highcharts/js/highcharts.js') ?>"></script>
<script type="text/javascript">

var invoice= {

	loadData: function() {
		$('#tblcheckin').DataTable({
		/*
        	"ajax": {
                "url": "<?php echo site_url('invoice') ?>",
                "type": "POST",
                "data": function(d) {
                    //d.po = document.getElementById('po').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                   // d.dateTo = document.getElementById('dateTo').value;
                },
            },
		 
            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center", "width": 30},
               	{"data": "customer_name", "orderable": true},
				{"data": "booking_code", "orderable": true},
				{"data": "service_name", "orderable": true},
				{"data": "payment_name", "orderable": true},
                {"data": "invoice_number", "orderable": true},
				{"data": "amount", "orderable": true},
                {"data": "invoice_date", "orderable": true},
            ],
            */
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
            "searching":false,
        	"lengthMenu": [
                [10, 25, 50, 100,-1],
                [10, 25, 50, 100,"all"]
            ],
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "asc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblcheckin').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },

            "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 7 ).footer() ).html(
                pageTotal
            );
        },
            buttons: [
                {
                    extend: 'excel',
                    },
            ]
		});

        $('#export_tools  > a.tool-action').on('click', function() {
            var data_tables = $('#tblcheckin').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#tblcheckin').DataTable().ajax.reload();
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
	invoice.init();

    $('#ship_id').select2({width:"100%"}, {height:"500px"});
    $('.select2-selection').css('height', '34px')


	$('.date').datepicker({
		format: 'yyyy-mm-dd',
		changeMonth: true,
		changeYear: true,
		autoclose: true,
		todayHighlight: true,
		
	}).on('changeDate',function(e) {
		//invoice.reload();
	});
	
	$("#cari").on("click",function(){
		invoice.reload();
	});
});
</script>