<script type="text/javascript">
	class MyData
	{
	    loadData=()=> 
	    {
	        $('#dataTables').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('transaction/pindah_kapal/checkScheduleCodePassenger/'); ?>",
	                "type": "post",
	                "data": function(d) {
	                    d.ticketNumber = document.getElementById('ticketNumber').value;
	                },
	            },
	            "serverSide": true,
	            "processing": true,
	            "columns": [
	                {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
	                {"data": "boarding_date", "orderable": true, "className": "text-right"},
	                {"data": "ticket_number", "orderable": true, "className": "text-right"},
	                {"data": "customer_name", "orderable": true, "className": "text-right"},
	                {"data": "customer_gender", "orderable": true, "className": "text-right"},
	                {"data": "customer_age", "orderable": true, "className": "text-right"},
	                {"data": "customer_city", "orderable": true, "className": "text-right"},
	                {"data": "id_type_name", "orderable": true, "className": "text-right"},
	                {"data": "id_number", "orderable": true, "className": "text-right"},
	                {"data": "port_name", "orderable": false, "className": "text-right"},
	                {"data": "dock_name", "orderable": false, "className": "text-right"},
	                {"data": "ship_name", "orderable": false, "className": "text-right"},
	                {"data": "schedule_code", "orderable": false, "className": "text-right"},
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
	            "order": [[ 0, "desc" ]],
	            "initComplete": function () {
	                var $searchInput = $('#dataTables_filter input');
	                var data_tables = $('#dataTables').DataTable();
	                $searchInput.unbind();
	                $searchInput.bind('keyup', function (e) {
	                    if (e.keyCode == 13 || e.whiche == 13) {
	                        data_tables.search(this.value).draw();
	                    }
	                });
	            },
	        });
	    }

	    init=()=>
	    {
	        if (!jQuery().DataTable) {
	            return;
	        }
	        this.loadData();
	    }

	    loadData2 = ()=> {
	        $('#dataTables2').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('transaction/pindah_kapal/checkScheduleCodeVehicle/'); ?>",
	                "type": "post",
	                "data": function(d) {
	                    d.ticketNumber = document.getElementById('ticketNumber').value;
	                },
	            },
	            "serverSide": true,
	            "processing": true,
	            "columns": [
	                {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
	                {"data": "boarding_date", "orderable": true, "className": "text-right"},
	                {"data": "ticket_number", "orderable": true, "className": "text-right"},
	                {"data": "id_number", "orderable": true, "className": "text-right"},
	                {"data": "name", "orderable": true, "className": "text-right"},
	                {"data": "length", "orderable": true, "className": "text-right"},
	                {"data": "height", "orderable": true, "className": "text-right"},
	                {"data": "weight", "orderable": true, "className": "text-right"},
	                {"data": "port_name", "orderable": false, "className": "text-right"},
	                {"data": "dock_name", "orderable": false, "className": "text-right"},
	                {"data": "ship_name", "orderable": false, "className": "text-right"},
	                {"data": "schedule_code", "orderable": false, "className": "text-right"},
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
	            "order": [[ 0, "desc" ]],
	            "initComplete": function () {
	                var $searchInput = $('#dataTables2_filter input');
	                var data_tables = $('#dataTables2').DataTable();
	                $searchInput.unbind();
	                $searchInput.bind('keyup', function (e) {
	                    if (e.keyCode == 13 || e.whiche == 13) {
	                        data_tables.search(this.value).draw();
	                    }
	                });
	            },
	        });
	    }

	    init2=()=> {
	        if (!jQuery().DataTable) {
	            return;
	        }
	        this.loadData2();
	    }

	    reload=(id)=> {
	        $('#'+id).DataTable().ajax.reload();
	    }

	}
</script>