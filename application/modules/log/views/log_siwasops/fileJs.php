<script type="text/javascript">
	class MyData{

	    loadData=()=> {
	        $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('log/log_siwasops') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port=$("#port").val();
                        d.searchData = $('#searchData').val();
                        d.searchName = $("#searchData").attr('data-name');

                    },
                },



                "serverSide": true,
                "processing": true,
                "searching":false,
                "columns": [{
                        "data": "no",
                        "orderable": false,
                        "className": "text-center",
                        "width": 5
                    },
                    {
                        "data": "boarding_code",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "boarding_date",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "kapal",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "pelabuhan",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "dermaga",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "send_date",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "status",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "actions",
                        "orderable": false,
                        "className": "text-center"
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
                    //console.log(allRow);
                    if (allRow.json.recordsTotal) {
                        $('.download').prop('disabled', false);
                    } else {
                        $('.download').prop('disabled', true);
                    }
                },
                


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