<script type="text/javascript">
	class MyData{		
	    loadData=()=> {

	        $('#dataTables').DataTable({
	            "ajax": {
	                "url": "<?php echo site_url('transaction/gate_in') ?>",
	                "type": "POST",
	                "data": function(d) {
	                    d.dateTo = document.getElementById('dateTo').value;
	                    d.dateFrom = document.getElementById('dateFrom').value;
	                    d.port = document.getElementById('port').value;
	                   	d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');

	                },
	            },	         
	            "serverSide": true,
	            "processing": true,
	            "columns": [
	                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
	                    {"data": "created_on", "orderable": false, "className": "text-left"},
	                    {"data": "booking_code", "orderable": false, "className": "text-left"},
	                    {"data": "ticket_number", "orderable": false, "className": "text-left"},
	                    {"data": "id_number", "orderable": false, "className": "text-left"},
	                    {"data": "passanger_name", "orderable": false, "className": "text-left"},
	                    {"data": "service_name", "orderable": false, "className": "text-left"},
	                    {"data": "terminal_name", "orderable": false, "className": "text-left"},
	                    {"data": "port_name", "orderable": false, "className": "text-left"},
	                    {"data": "boarding_expired", "orderable": false, "className": "text-left"},
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
	            "searching":false,
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

	            fnDrawCallback: function(allRow)
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
	                //console.log(allRow);
	                if(allRow.json.recordsTotal)
	                {
	                    $('.download').prop('disabled',false);
	                }
	                else
	                {
	                    $('.download').prop('disabled',true);
	                }
	            },
	            "columnDefs" : [
	                {
	                    targets : [3],
	                    visible:<?php echo $gs ?>

	                }
	           ],


	        });

	        $('#export_tools > li > a.tool-action').on('click', function() {
	            var data_tables = $('#dataTables').DataTable();
	            var action = $(this).attr('data-action');

	            data_tables.button(action).trigger();
	        });
	    }

	    reload=()=> {
	        $('#dataTables').DataTable().ajax.reload();
	    }

	    init=()=> {
	        if (!jQuery().DataTable) {
	            return;
	        }

	        this.loadData();
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