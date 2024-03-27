<script type="text/javascript">

	class MyData
	{
        loadData=()=> {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/booking_daily/penumpang') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port = document.getElementById('port').value;
                        d.kelas = document.getElementById('kelas').value;
                        d.shift = document.getElementById('shift').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
                    },
                    complete: function(){
					$("#btnSearch").button('reset');
                    $("#tabPenumpang").button('reset');
					$("#table_penumpang").show();
				}
                },
                "serverSide": true,
                "processing": true,
                "columns": [{
                        "data": "no",
                        "orderable": false,
                        "className": "text-center",
                        "width": 5
                    },
                    {
                        "data": "booking_code",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "created_on",
                        "orderable": true,
                        "className": "text-left"
                    },                    
                    {
                        "data": "depart_date",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "customer_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "nik",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "jenis_identitas",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "gender",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "tipe_penumpang",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "kelas",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "port_origin",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "service_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "fare",
                        "orderable": true,
                        "className": "text-right"
                    },
                    {
                        "data": "shift",
                        "orderable": true,
                        "className": "text-left"
                    }                    
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
                    var searchInput = $('div.table_penumpang_filter input');
                    var data_tables = $('#table_penumpang').DataTable();
                    searchInput.unbind();
                    searchInput.bind('keyup', function(e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                fnDrawCallback: function(allRow) {
                    console.log(allRow);
                    if (allRow.json.recordsTotal) {
                        $('.download').prop('disabled', false);
                    } else {
                        $('.download').prop('disabled', true);
                    }
                }
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
                    "url": "<?php echo site_url('transaction/booking_daily/kendaraan') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port = document.getElementById('port').value;
                        d.kelas = document.getElementById('kelas').value;
                        d.shift = document.getElementById('shift').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
                    },
                    complete: function(){
					$("#btnSearch").button('reset');
                    $("#tabKendaraan").button('reset');
					$("#table_kendaraan").show();
				}
                },
                "serverSide": true,
                "processing": true,
                "searching": true,
                "columns": [{
                        "data": "number",
                        "orderable": false,
                        "className": "text-center",
                        "width": 5
                    },
                    {
                        "data": "booking_code",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "created_on",
                        "orderable": true,
                        "className": "text-left"
                    },                    
                    {
                        "data": "depart_date",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "plat",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "vehicle_class",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "kelas",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "customer",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "origin",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "weighbridge",
                        "orderable": true,
                        "className": "text-right"
                    },                    
                    {
                        "data": "fare",
                        "orderable": true,
                        "className": "text-right"
                    },
                    {
                        "data": "shift",
                        "orderable": true,
                        "className": "text-right"
                    },                    
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
                "order": [
                    [0, "desc"]
                ],
                "initComplete": function() {
                    var searchInput = $('div.table_kendaraan_filter input');
                    var data_tables = $('#table_kendaraan').DataTable();
                    searchInput.unbind();
                    searchInput.bind('keyup', function(e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                fnDrawCallback: function(allRow) {
                    console.log(allRow);
                    if (allRow.json.recordsTotal) {
                        $('.download1').prop('disabled', false);
                    } else {
                        $('.download1').prop('disabled', true);
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