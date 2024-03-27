<script type="text/javascript">
	class MyData{

        loadData=()=> 
        {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/verifikator') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port = document.getElementById('port').value;
                        d.shipClass = document.getElementById('shipClass').value;
                        d.passangerType = document.getElementById('passangerType').value;
                        d.vehicleClass = document.getElementById('vehicleClass').value;
                        d.service = document.getElementById('service').value;
                        d.statusTicket = document.getElementById('statusTicket').value;
                        d.dataValidasi = document.getElementById('dataValidasi').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.dataJam=document.getElementById('dataJam').value;
                        d.searchName=$("#searchData").attr('data-name');
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                    
                    {"data": "no","orderable": false,"className": "text-center","width": 5},                   
                    {"data": "booking_code","orderable": true,"className": "text-left"},
                    {"data": "ticket_number","orderable": true,"className": "text-left"},
                    {"data": "origin_name","orderable": true,"className": "text-left"},
                    {"data": "service_name","orderable": true,"className": "text-left"},
                    {"data": "ship_class_name","orderable": true,"className": "text-left"},
                    {"data": "golongan_knd","orderable": true,"className": "text-left"},
                    {"data": "golongan_pnp","orderable": true,"className": "text-left"},                        
                    {"data": "plat_no","orderable": true,"className": "text-left"},
                    {"data": "passanger_name","orderable": true,"className": "text-left"},
                    {"data": "id_type_name","orderable": true,"className": "text-left"},
                    {"data": "no_identitas","orderable": true,"className": "text-left"},
                    {"data": "age","orderable": true,"className": "text-right"},
                    {"data": "gender","orderable": true,"className": "text-center"},
                    {"data": "city","orderable": true,"className": "text-left"},
                    {"data": "tanggal_masuk_pelabuhan","orderable": true,"className": "text-left"},
                    {"data": "depart_time_start","orderable": true,"className": "text-left"},
                    {"data": "status_ticket","orderable": true,"className": "text-left"},
                    {"data": "checkin_date","orderable": true,"className": "text-left"},
                    {"data": "gatein_date","orderable": true,"className": "text-left"},
                    {"data": "boarding_date","orderable": true,"className": "text-left"},
                    {"data": "approved_status","orderable": true,"className": "text-center"},
                    {"data": "user_verified","orderable": true,"className": "text-left"},
                    {"data": "approved_date","orderable": true,"className": "text-left"},
                    {"data": "terminal_name","orderable": true,"className": "text-left"},
                    {"data": "terminal_code","orderable": true,"className": "text-left"},                       

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
                "order": [
                    [0, "desc"]
                ],
                "initComplete": function() {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function(e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },

                fnDrawCallback: function(allRow) {
                    // console.log(allRow);
                    if (allRow.json.recordsTotal) {
                        $('.download').prop('disabled', false);
                    } else {
                        $('.download').prop('disabled', true);
                    }
                }
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

	    formatDate=(date)=> {
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