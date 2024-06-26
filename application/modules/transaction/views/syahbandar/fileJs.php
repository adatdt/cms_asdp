<script type="text/javascript">

    class MyData
    {
        loadData =()=> {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/syahbandar') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port_origin = document.getElementById('port_origin').value;
                        d.port_destination = document.getElementById('port_destination').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
                    },
                },


             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "open_boarding_date", "orderable": true, "className": "text-left"},
                        {"data": "boarding_code", "orderable": true, "className": "text-left"},
                        {"data": "schedule_date", "orderable": true, "className": "text-left"},
                        {"data": "ship_name", "orderable": true, "className": "text-left"},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "dock_name", "orderable": true, "className": "text-left"},
                        {"data": "port_destination", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "sail_date", "orderable": true, "className": "text-left"},
                        {"data": "ket", "orderable": true, "className": "text-center"},
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
					let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
					let getToken = allRow.json[getTokenName];
					csfrData[getTokenName] = getToken;
					$.ajaxSetup({
						data: csfrData
					});                    
                    //console.log(allRow);
                    if(allRow.json.recordsTotal>0)
                    {
                        $('#download_excel').prop('disabled',false);
                    }
                    else
                    {
                        $('#download_excel').prop('disabled',true);
                    }
                }
            });

        }

        loadData2 =()=> 
        {
            $('#dataTables2').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/syahbandar/data_approve') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo2').value;
                        d.dateFrom = document.getElementById('dateFrom2').value;
                        d.port_origin = document.getElementById('port_origin2').value;
                        d.port_destination = document.getElementById('port_destination2').value;
                        d.searchData=document.getElementById('searchData2').value;
                        d.searchName=$("#searchData2").attr('data-name');
                    },
                },


             
                "serverSide": true,
                "searching": false,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "open_boarding_date", "orderable": true, "className": "text-left"},
                        {"data": "boarding_code", "orderable": true, "className": "text-left"},
                        {"data": "schedule_date", "orderable": true, "className": "text-left"},
                        {"data": "ship_name", "orderable": true, "className": "text-left"},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "dock_name", "orderable": true, "className": "text-left"},
                        {"data": "port_destination", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "sail_date", "orderable": true, "className": "text-left"},
                        {"data": "ket", "orderable": true, "className": "text-center"},
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
					let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
					let getToken = allRow.json[getTokenName];
					csfrData[getTokenName] = getToken;
					$.ajaxSetup({
						data: csfrData
					});                    
                    //console.log(allRow);
                    if(allRow.json.recordsTotal>0)
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

        reload=(id)=> {
            $(id).DataTable().ajax.reload();
        }

        init=()=> {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }

        init2=()=> {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData2();
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