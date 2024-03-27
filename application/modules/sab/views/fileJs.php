<script type="text/javascript">	

var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });

class MyData{
	loadDataPnp=()=> 
	{
		$('#tabel_penumpang').DataTable({
			"ajax": {
				"url": "<?php echo site_url('sab/penumpang') ?>",
				"type": "POST",
				"data": function(d) {
					d.dateTo = document.getElementById('dateTo').value;
					d.dateFrom = document.getElementById('dateFrom').value;
					d.port_origin = document.getElementById('port_origin').value;
					d.port_destination = document.getElementById('port_destination').value;
					d.searchData=document.getElementById('searchData').value;
					d.searchName=$("#searchData").attr('data-name');
				},
				complete: function(){
					$("#tabPenumpang").button('reset');
					$("#tabel_penumpang").show();
				}
			},
			"serverSide": true,
			"processing": true,
			"columns": [
					{"data": "number", "orderable": false, "className": "text-center" , "width": 5},
					{"data": "gatein_date", "orderable": true, "className": "text-left"},
					{"data": "ticket_number", "orderable": true, "className": "text-left"},
					{"data": "penumpang", "orderable": true, "className": "text-left"},
					{"data": "golongan", "orderable": true, "className": "text-left"},
					{"data": "instansi", "orderable": true, "className": "text-left"},
					{"data": "service", "orderable": true, "className": "text-left"},
					{"data": "port", "orderable": true, "className": "text-left"},
					{"data": "ship", "orderable": true, "className": "text-left"},
					{"data": "actions", "orderable": false, "className": "text-center"},
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
			"searching": false,
			"pagingType": "bootstrap_full_number",
			"order": [[ 0, "desc" ]],
			"initComplete": function () {
				var $searchInput = $('div.tabel_penumpang_filter input');
				var data_tables = $('#tabel_penumpang').DataTable();
				$searchInput.unbind();
				$searchInput.bind('keyup', function (e) {
					if (e.keyCode == 13 || e.whiche == 13) {
						data_tables.search(this.value).draw();
					}
				});
			},
			"fnDrawCallback": function(allRow) 
			{
				let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
				let getToken = allRow.json[getTokenName];
				csfrData[getTokenName] = getToken;
				if( allRow.json[getTokenName] == undefined )
				{
					csfrData[allRow.json['csrfName']] = allRow.json['tokenHash'];
				}
				$.ajaxSetup({
					data: csfrData
				});
			}
		});

		$('#export_tools > li > a.tool-action').on('click', function() {
			var data_tables = $('#tabel_penumpang').DataTable();
			var action = $(this).attr('data-action');

			data_tables.button(action).trigger();
		});
	}

	initPnp=()=> {
		if (!jQuery().DataTable) {
			return;
		}

		this.loadDataPnp();
	}

	loadDataKnd=()=> 
	{
		$('#tabel_kendaraan').DataTable({
			"ajax": {
				"url": "<?php echo site_url('sab/kendaraan') ?>",
				"type": "POST",
				"data": function(d) {
					d.dateTo = document.getElementById('dateTo').value;
					d.dateFrom = document.getElementById('dateFrom').value;
					d.port_origin = document.getElementById('port_origin').value;
					d.port_destination = document.getElementById('port_destination').value;
					d.searchData=document.getElementById('searchData').value;
					d.searchName=$("#searchData").attr('data-name');
				},
				complete: function(){
					$("#tabKendaraan").button('reset');
					$("#tabel_kendaraan").show();
				}
			},
			"serverSide": true,
			"processing": true,
			"columns": [
					{"data": "number", "orderable": false, "className": "text-center" , "width": 5},
					{"data": "gatein_date", "orderable": true, "className": "text-left"},
					{"data": "ticket_number", "orderable": true, "className": "text-left"},
					{"data": "plat", "orderable": true, "className": "text-left"},
					{"data": "golongan", "orderable": true, "className": "text-left"},
					{"data": "instansi", "orderable": true, "className": "text-left"},
					{"data": "service", "orderable": true, "className": "text-left"},
					{"data": "port", "orderable": true, "className": "text-left"},
					{"data": "ship", "orderable": true, "className": "text-left"},
					{"data": "total_passanger", "orderable": true, "className": "text-center"},
					{"data": "actions", "orderable": false, "className": "text-center"},
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
			"searching": false,
			"pagingType": "bootstrap_full_number",
			"order": [[ 0, "desc" ]],
			"initComplete": function () {
				var $searchInput = $('div.tabel_kendaraan_filter input');
				var data_tables = $('#tabel_kendaraan').DataTable();
				$searchInput.unbind();
				$searchInput.bind('keyup', function (e) {
					if (e.keyCode == 13 || e.whiche == 13) {
						data_tables.search(this.value).draw();
					}
				});
			},
			"fnDrawCallback": function(allRow) 
			{
				let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
				let getToken = allRow.json[getTokenName];
				csfrData[getTokenName] = getToken;
				if( allRow.json[getTokenName] == undefined )
				{
					csfrData[allRow.json['csrfName']] = allRow.json['tokenHash'];
				}
				$.ajaxSetup({
					data: csfrData
				});
			}
		});

		$('#export_tools > li > a.tool-action').on('click', function() {
			var data_tables = $('#tabel_kendaraan').DataTable();
			var action = $(this).attr('data-action');

			data_tables.button(action).trigger();
		});
	}

	initKnd() 
	{
		if (!jQuery().DataTable) {
			return;
		}

		this.loadDataKnd();
	}
	
	reload =(id)=> {
		$('#'+id).DataTable().ajax.reload();
	}
	formatDate(date) {
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		return [year, month, day].join('-');
	}
	changeSearch(x,name)
	{
		$("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
		$("#searchData").attr('data-name', name);

	}	    	    

}

</script>