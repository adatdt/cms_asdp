<script type="text/javascript">
	class MyData{

	    loadDataHistory=()=>
	    {
	        $('#dataTables').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('transaction/muntah_kapal/history') ?>",
	                "type": "POST",
	                "data": function(d) {
	                    d.dateTo = document.getElementById('dateTo').value;
	                    d.dateFrom = document.getElementById('dateFrom').value;
	                    d.ship = document.getElementById('ship').value;
	                    d.port = document.getElementById('port').value;
	                   	d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
	                },
	            },
	         
	            "serverSide": true,
	            "processing": true,
	            "columns": [
	                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
	                    {"data": "date", "orderable": true, "className": "text-center"},
	                    {"data": "ship_name", "orderable": true, "className": "text-left"},
	                    {"data": "port_name", "orderable": true, "className": "text-left"},
	                    {"data": "boarding_code", "orderable": true, "className": "text-left"},
	                    {"data": "schedule_code", "orderable": true, "className": "text-left"},
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
	    }
	    initHistory=()=> {
	        if (!jQuery().DataTable) {
	            return;
	        }

	        this.loadDataHistory();
	    }

	    loadDataPnp=()=>
	    {
	        $('#dataTables').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('transaction/muntah_kapal/checkTicketNumberPassenger/'); ?>",
	                "type": "post",
	                "data": function(d) {
	                    d.ticketNumber = $('#ticketNumber').tagsinput('items');
	                    d.type = document.getElementById('type').value;
	                },
	            },
	            "serverSide": true,
	            "processing": true,
	            "columns": [
	                {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
	                {"data": "boarding_date", "orderable": true, "className": "text-left"},
	                {"data": "ticket_number", "orderable": true, "className": "text-right"},
	                {"data": "customer_name", "orderable": true, "className": "text-left"},
	                {"data": "customer_gender", "orderable": true, "className": "text-left"},
	                {"data": "customer_age", "orderable": true, "className": "text-right"},
	                {"data": "customer_city", "orderable": true, "className": "text-left"},
	                {"data": "id_type_name", "orderable": true, "className": "text-left"},
	                {"data": "id_number", "orderable": true, "className": "text-right"},
	                {"data": "port_name", "orderable": true, "className": "text-left"},
	                {"data": "dock_name", "orderable": true, "className": "text-left"},
	                {"data": "ship_name", "orderable": true, "className": "text-left"},
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

	    initPnp=()=>
	    {
	        if (!jQuery().DataTable) {
	            return;
	        }
	        this.loadDataPnp();
	    }	    

	    loadDataKnd=()=> {
	        $('#dataTables2').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('transaction/muntah_kapal/checkTicketNumberVehicle/'); ?>",
	                "type": "post",
	                "data": function(d) {
	                    d.ticketNumber = $('#ticketNumber').tagsinput('items');
	                    d.type = document.getElementById('type').value;
	                },
	            },
	            "serverSide": true,
	            "processing": true,
	            "columns": [
	                {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
	                {"data": "boarding_date", "orderable": true, "className": "text-left"},
	                {"data": "ticket_number", "orderable": true, "className": "text-right"},
	                {"data": "id_number", "orderable": true, "className": "text-left"},
	                {"data": "name", "orderable": true, "className": "text-left"},
	                {"data": "length", "orderable": true, "className": "text-right"},
	                {"data": "height", "orderable": true, "className": "text-right"},
	                {"data": "weight", "orderable": true, "className": "text-right"},
	                {"data": "port_name", "orderable": true, "className": "text-left"},
	                {"data": "dock_name", "orderable": true, "className": "text-left"},
	                {"data": "ship_name", "orderable": true, "className": "text-left"},
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

	    initKnd=()=> {
	        if (!jQuery().DataTable) {
	            return;
	        }
	        this.loadDataKnd();
	    }



	    reload=(id)=> {
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