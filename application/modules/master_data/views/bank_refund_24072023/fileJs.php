<script type="text/javascript">
	
	class MyData{
	    loadData() {
	        $('#dataTables').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('master_data/bank_refund') ?>",
	                "type": "POST",
	                "data": function(d) {
	                    // d.port = document.getElementById('port').value;
	                    // d.team = document.getElementById('team').value;
	                },
	            },

	            "serverSide": true,
	            "processing": true,
	            "columns": [
	                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
	                    {"data": "bank_abbr", "orderable": true, "className": "text-left"},
	                    {"data": "bank_name", "orderable": true, "className": "text-left"},
	                    {"data": "transfer_fee", "orderable": true, "className": "text-right"},
	                    {"data": "status", "orderable": true, "className": "text-center"},
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

	    reload() {
	        $('#dataTables').DataTable().ajax.reload();
	    }

	    init() {
	        if (!jQuery().DataTable) {
	            return;
	        }

	        this.loadData();
	    }

	}
</script>