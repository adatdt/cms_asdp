<script type="text/javascript">
	class MyData
	{
        loadData=()=> {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('manifest/add_manifest') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port_origin = document.getElementById('port_origin').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');

                    },
                },


             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "schedule_date", "orderable": true, "className": "text-left"},
                        {"data": "schedule_code", "orderable": true, "className": "text-left"},
                        {"data": "boarding_code", "orderable": true, "className": "text-left"},
                        {"data": "created_on", "orderable": true, "className": "text-left"},
                        {"data": "ship_name", "orderable": true, "className": "text-left"},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "dock_name", "orderable": true, "className": "text-left"},
                        {"data": "port_destination", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "sail_date", "orderable": true, "className": "text-left"},
                        {"data": "schedule_name", "orderable": true, "className": "text-left"},
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
                "searching":false,
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div#dataTables_filter input');
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

        init=()=> {
            if (!jQuery().DataTable) {
                return;
            }
            this.loadData();
        }
        loadData2 = () =>{
            $('#dataTables2').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('manifest/add_manifest/dataListPassanger') ?>",
                    "type": "POST",
                    "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.port = document.getElementById('port_origin').value;
                    d.searchData=document.getElementById('searchData').value;
                    d.searchName=$("#searchData").attr('data-name');
                    },
                },


             
                "serverSide": true,
                "processing": true,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "boarding_date", "orderable": true, "className": "text-left"},
                    {"data": "schedule_code", "orderable": true, "className": "text-left"},
                    {"data": "boarding_code", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "dock_name", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "passanger_name", "orderable": true, "className": "text-left"},
                    {"data": "age", "orderable": true, "className": "text-left"},
                    {"data": "gender", "orderable": true, "className": "text-left"},
                    {"data": "passanger_type_name", "orderable": true, "className": "text-left"},
                    {"data": "service_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-left"},
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
                    var $searchInput = $('div#dataTables2_filter input');
                    var data_tables = $('#dataTables2').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                "fnDrawCallback": function( allData ) {
                    if(allData.json.recordsTotal)
                    {
                        $('#downloadExcel2').prop('disabled',false);
                    }
                    else
                    {
                        $('#downloadExcel2').prop('disabled',true);
                    }
                }
            });

        }

        init2 =()=> {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData2();
        }              

        loadData3=()=>
        {
            $('#dataTables3').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('manifest/add_manifest/dataListVehicle') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port = document.getElementById('port_origin').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
                    },
                },
            
                "serverSide": true,
                "processing": true,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "boarding_date", "orderable": true, "className": "text-left"},
                    {"data": "schedule_code", "orderable": true, "className": "text-left"},
                    {"data": "boarding_code", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "dock_name", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "first_passanger", "orderable": true, "className": "text-left"},
                    {"data": "id_number", "orderable": true, "className": "text-left"},
                    {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                    {"data": "service_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_name", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-left"},
                    {"data": "total_passanger", "orderable": true, "className": "text-left"},
                  
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
                    var $searchInput = $('div#dataTables3_filter input');
                    var data_tables = $('#dataTables3').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                "fnDrawCallback": function( allData ) {
                    if(allData.json.recordsTotal)
                    {
                        $('#downloadExcel3').prop('disabled',false);
                    }
                    else
                    {
                        $('#downloadExcel3').prop('disabled',true);
                    }
                },
            });
        }

        init3 =()=>{
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData3();
        }     

        reload=(id)=> {
            $('#'+id).DataTable().ajax.reload();
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