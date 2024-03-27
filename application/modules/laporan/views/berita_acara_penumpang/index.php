<div class="page-content-wrapper">
	<div class="page-content">
		<div class="portlet box blue-hoki">
			<div class="portlet-title">
				<div class="caption">
					<?php echo $title; ?>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-inline">
					<div class="row">
						<div class="col-md-12"> 
							<div class="input-group select2-bootstrap-prepend">
								<div class="input-group-addon">PELABUHAN </div>
								<select id="port" class="form-control js-data-example-ajax select2" dir="">
									<option value="">All</option>
									<?php foreach ($pelabuhan as $key => $value) { ?>
										<option value="<?= $this->enc->encode($value->id) ?>"><?=$value->name ?></option>
									<?php } ?>
								</select>
							</div>  
							<div class="input-group">
								<div class="input-group-addon">TANGGAL</div>
								<input class="form-control input-small date" id="dateto" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
								<div class="input-group-addon"><i class="icon-calendar"></i></div>
							</div> 
							<div class="input-group select2-bootstrap-prepend">
								<div class="input-group-addon">REGU </div>
								<select id="regu" class="form-control js-data-example-ajax select2" dir="">
									<option value="">All</option>
									<?php foreach ($regu as $key => $value) { ?>
										<option value="<?= $this->enc->encode($value->team_code) ?>"><?=$value->team_name ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="input-group select2-bootstrap-prepend">
								<div class="input-group-addon">SHIFT </div>
								<select id="shift" class="form-control js-data-example-ajax select2" dir="">
									<option value="">All</option>
									<?php foreach ($shift as $key => $value) { ?>
										<option value="<?= $this->enc->encode($value->id) ?>"><?=$value->shift_name ?></option>
									<?php } ?>
								</select>
							</div>
							<button type="button" class="btn btn-info" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">cari</button>
						</div>
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
					<thead>
						<tr>
							<th>PELABUHAN</th>
							<th>TANGGAL</th>
							<th>REGU</th>
							<th>SHIFT</th>
							<th>ACTION</th>
						</tr>
					</thead>
					<tfoot></tfoot>
				</table>
			</div>
		</div>        
	</div>
</div>
<script type="text/javascript">
var berita_acara_penumpang= {
    loadData: function() {
        var numericColumn = [3, 4, 5, 6, 7];
        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function(data, column, row, node) {
                        return numericColumn.indexOf(column) >= 0 ? parseInt(data.toString().replace(/\./g, '')) : data;
                    }
                }
            }
        };

        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporan/berita_acara_penumpang') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port= document.getElementById('port').value;
                    d.regu= document.getElementById('regu').value;
                    d.shift= document.getElementById('shift').value;
                    d.date= document.getElementById('dateto').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "port_name", "orderable": false,"className": "text-center"},
                {"data": "assignment_date", "orderable": false, "searchable": false, "className": "text-center"},
                {"data": "team_name", "orderable": false,"className": "text-center"},
                {"data": "shift_name", "orderable": false,"className": "text-center"},
                {"data": "actions","orderable": false,"className": "text-center"},
            ],
           
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",
                },
                "processing": "Proses.....",
                "emptyTable": "Tidak ada data",
                "info": "Total _TOTAL_ data",
                "infoEmpty": "Total 0 data",
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

            "bStateSave": true,
            "searching":false,
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
            "paging": false,
            "order": [[1, "desc" ]],

            "fnDrawCallback": function(data) {
                $('#total').html('<h4>Total Pendapatan Rp.'+data.json.total+'</h4>');
            },

            "initComplete": function (data) {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#dataTables').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });

        // $('#export_tools  > a.tool-action').on('click', function() {
        //     var data_tables = $('#dataTables').DataTable();
        //     var action = $(this).attr('data-action');

        //     data_tables.button(action).trigger();
        // });
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
    berita_acara_penumpang.init();

    $('#dateto').datepicker({
        format: 'yyyy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        startDate: $('#datefrom').val(),
        endDate: new Date(),
    });

    $("#cari").on("click",function(){
        $(this).button('loading');
        berita_acara_penumpang.reload();
        $('#dataTables').on('draw.dt', function() {
            $("#cari").button('reset');
        });
    });

    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);
});
</script>