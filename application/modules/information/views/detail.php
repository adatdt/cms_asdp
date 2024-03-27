<?php $this->load->helper('nutech_helper'); ?>
<div class="page-content-wrapper">
  <div class="page-content">
    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li>
          <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
          <i class="fa fa-circle"></i>
        </li>
        <li>
          <?php echo '<a href="' . $url_parent1 . '">' . $parent1 . '</a>'; ?>
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
            <?php echo generate_button('schedule', 'view', '<a href="' . site_url('sail') . '" class="btn btn-warning">Kembali</a>'); ?>
          </div>
        </div>
      </div>
      <div class="portlet-body">
		<!-- Start row -->
		<ul class="nav nav-tabs">
			<li class="<?php echo ($tab == 'passanger') ? 'active' : ''; ?>"><a href="#passanger" data-toggle="tab"><span style="font-size:13px width: 50px text-align: center" class="widget-caption btn ">Penumpang</span></a></li>
			<li class="<?php echo ($tab == 'vehicle') ? 'active' : ''; ?>"><a href="#vehicle" data-toggle="tab"><span style="font-size:13px width: 140px text-align: center" class="widget-caption btn ">Kendaraan </span></a></li>
		</ul>
		
		<form method="post" action="<?php echo site_url('sail/approve');?>" > 		
				<br>
		<div class="tab-content">
			<div class="tab-pane <?php echo ($tab == 'passanger') ? 'active' : ''; ?>" id="passanger">
			<span id='export_tools' > <a href="javascript:;" data-action="0" class="tool-action btn btn-warning" id="export_tools"><i class="icon-doc"></i> Export</a></span>
			<br />				
				<table class="table table-striped"  id="dataTables">
					<thead>
						<tr style="background:#FFCC00; color:#FFFFFF;" >
							<td>Nama</td><td>NO KTP</td><td>Jenis Kelamin</td><td>Tanggal Lahir</td><td>Kota Asal</td><td>Nomer Tiket</td> 
							<td>Tanggal Keberangkatan</td><td>Jam Keberangkatan</td><td>Nama Kapal</td> 
						</tr>
					</thead>
					<?php if (empty($passanger))
					{
						echo"";
					}
					else
					{
					?>
					<tbody>
					<?php 
					foreach ($passanger as $passanger ){?>
					<tr>
						<td > <?php echo $passanger->name ?><input type="hidden" value='<?php echo $passanger->booking_passanger_id; ?>' name='booking_passanger_id[]' /></td>
						<td > <?php echo $passanger->id_number ?></td>
						<td > <?php echo $passanger->gender=='L'?'Laki-laki':'Perempuan'; ?></td>
						<td > <?php echo format_date($passanger->birth_date) ?></td>
						<td > <?php echo $passanger->city ?></td>
						<td > <?php echo $passanger->ticket_number ?></td>
						<td > <?php echo format_date($passanger->depart_date); ?></td>
						<td > <?php echo format_time($passanger->departure); ?></td>
						<td > <?php echo $passanger->ship_name; ?></td>
					</tr>
					<?php }
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="tab-pane <?php echo ($tab == 'vehicle') ? 'active' : ''; ?>" id="vehicle">	
				<span id='export_tools2' > <a href="javascript:;" data-action="0" class="tool-action btn btn-warning" id="export_tools"><i class="icon-doc"></i> Export</a></span>
				<br />
							
				<table class="table table-striped" id="dataTables2">
					<thead>
						<tr style="background:#FFCC00; color:#FFFFFF;" >
							<td>Nama</td><td>Golongan</td><td>No Tiket</td><td>Tanggal Keberangkatan</td><td>Jam Keberangkatan</td><td>Nama Kapal</td> 
						</tr>
					</thead>
					<?php 
					if (empty($vehicle))
					{
						echo " ";
					}
					else
					{
					 ?>
					<tbody>
					<?php 
					
					foreach ($vehicle as $vehicle){?>
					<tr>
						<td > <?php echo $vehicle->customer_name ?> <input type="hidden" value='<?php echo $vehicle->booking_vehicle_id; ?>' name='booking_vehicle_id[]' /></td>
						<td > <?php echo $vehicle->vehicle_name?></td>
						<td > <?php echo $vehicle->ticket_number ?></td>
						<td > <?php echo $vehicle->depart_date ?></td>
						<td > <?php echo $vehicle->departure ?></td>
						<td > <?php echo $vehicle->ship_name ?></td>
					</tr>
					<?php }
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<input type="hidden" value="<?php echo empty($passanger->schedule_time_id)? $vehicle->schedule_time_id:$passanger->schedule_time_id; ?>" name="schedule_time_id"/>
		<input type="hidden" value="<?php echo empty($passanger->shipid)? $vehicle->shipid:$passanger->shipid; ?>" name="ship_id"/>
		<input type="hidden" value="<?php echo empty($passanger->depart_date)? $vehicle->depart_date:$passanger->depart_date; ?>" name="depart_date"/>
		<input type="hidden" value="<?php echo empty($passanger->departure)? $vehicle->departure:$passanger->departure; ?>" name="departure"/>
		
		<?php echo $approve; ?>
		<!-- end row -->
		</form>
		
		
	</div> 
</div>

</div>


<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/highcharts/js/highcharts.js') ?>"></script>
<script type="text/javascript">

var manifest= {

	loadData: function() {
		$('#dataTables').DataTable({
				"pageLength": 1000,
				"searching" : false,
				"paging":   false,
				"ordering": false,
				"info":     false,
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
                [10, 25, 50, 100,500,1000],
                [10, 25, 50, 100,500,1000]
            ],
            "pageLength": 500,
            "pagingType": "bootstrap_full_number",
            "order": [[ 1, "desc" ]],
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
	                extend: 'excel',
	                className: 'btn green btn-outline',
		            },
	        ]

		});

        $('#export_tools >  a.tool-action').on('click', function() {
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

//------------------------
var manifest2= {

	loadData: function() {
		$('#dataTables2').DataTable({
				"pageLength": 1000,
				"searching" : false,
				"paging":   false,
				"ordering": false,
				"info":     false,
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
                [10, 25, 50, 100,500,1000],
                [10, 25, 50, 100,500,1000]
            ],
            "pageLength": 500,
            "pagingType": "bootstrap_full_number",
            "order": [[ 1, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#dataTables2').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
			
			buttons: [
                {
	                extend: 'excel',
	                className: 'btn green btn-outline',
		            },
	        ]

		});

        $('#export_tools2 >  a.tool-action').on('click', function() {
            var data_tables = $('#dataTables2').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
	},

	reload: function() {
		$('#dataTables2').DataTable().ajax.reload();
	},

	init: function() {
		if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
	}
};


$(document).ready(function() {

manifest.init();
manifest2.init();

/*
var data2 = $("#dataTables2").DataTable( {
	
	
		  dom: 'Bfrtip',
        buttons: [
           { extend:'excelHtml5', text:'Import data', className:'btn green btn-intline'},
			
        ],
		"pageLength": 1000,
		"searching" : false,
		"paging":   false,
        "ordering": false,
        "info":     false
    } );

*/
} );

</script>