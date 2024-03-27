<script type="text/javascript">

class MyData
	{
        loadData=()=> {
          $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/ticket_sobek') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port = document.getElementById('port').value;
                        d.shift = document.getElementById('shift').value;
                        d.ship_class = document.getElementById('ship_class').value;
						d.searchData=document.getElementById('searchData').value;
						d.searchName=$("#searchData").attr('data-name');
												
                    },
                },


             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "created_on", "orderable": true, "className": "text-left"},
                        {"data": "trx_date", "orderable": true, "className": "text-left"},
                        {"data": "trans_number", "orderable": true, "className": "text-left"},
                        {"data": "name", "orderable": true, "className": "text-left"},
                        {"data": "ticket_number_manual", "orderable": true, "className": "text-left"},
                        {"data": "ticket_number", "orderable": true, "className": "text-left"},
                        {"data": "gender", "orderable": true, "className": "text-left"},
                        {"data": "address", "orderable": true, "className": "text-left"},
                        {"data": "username", "orderable": true, "className": "text-left"},
                        {"data": "shift_name", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
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
                    var $searchInput = $('div #dataTables_filter input');
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
                    //console.log(allRow);
                    if(allRow.json.recordsTotal)
                    {
                        $('#download_excel').prop('disabled',false);
                    }
                    else
                    {
                        $('#download_excel').prop('disabled',true);
                    }
                }
            });

						$('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
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
                    "url": "<?php echo site_url('transaction/ticket_sobek/get_vehicle') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo2').value;
                        d.dateFrom = document.getElementById('dateFrom2').value;
                        d.port = document.getElementById('port2').value;
                        d.shift = document.getElementById('shift2').value;
                        d.ship_class = document.getElementById('ship_class2').value;
						d.searchData=document.getElementById('searchData2').value;
						d.searchName=$("#searchData2").attr('data-name');
                    },
                },
             
                "serverSide": true,
                "processing": true,

                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "created_on", "orderable": true, "className": "text-left"},
                        {"data": "trx_date", "orderable": true, "className": "text-left"},
                        {"data": "trans_number", "orderable": true, "className": "text-left"},
                        {"data": "name", "orderable": true, "className": "text-left"},
                        {"data": "ticket_number_manual", "orderable": true, "className": "text-left"},
                        {"data": "ticket_number", "orderable": true, "className": "text-left"},
                        {"data": "id_number", "orderable": true, "className": "text-left"},
                        {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                        {"data": "username", "orderable": true, "className": "text-left"},
                        {"data": "shift_name", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "total_passanger", "orderable": true, "className": "text-center"},
                ],
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
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
                    var $searchInput = $('div #dataTables2_filter input');
                    var data_tables = $('#dataTables2').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },

                fnDrawCallback: function(allRow)
                {
                    //console.log(allRow);
                    if(allRow.json.recordsTotal)
                    {
                        $('#download_excel2').prop('disabled',false);
                    }
                    else
                    {
                        $('#download_excel2').prop('disabled',true);
                    }
                }
            });

            $('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables2').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
            });

						}

        init2 =()=> {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData2();
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

			changeSearch2(x,name)
	    {
	    	$("#btnData2").html(`${x} <i class="fa fa-angle-down"></i>`);
	    	$("#searchData2").attr('data-name', name);

	    }

	}

</script>