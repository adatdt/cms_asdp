<script type="text/javascript">
    var table= {
        loadData: function() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/syahbandarv2') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port_origin = document.getElementById('port_origin').value;
                        d.port_destination = document.getElementById('port_destination').value;
                    },
                },


             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "created_on", "orderable": true, "className": "text-left"},
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
                        $('#download').prop('disabled',false);
                    }
                    else
                    {
                        $('#download').prop('disabled',true);
                    }
                }
            });

            $('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
            });
        },

        reload: function() {
            $('#dataTables').DataTable().ajax.reload();
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };


    var table2= {
        loadData: function() {
            $('#dataTables2').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/syahbandarv2/data_approve') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo2').value;
                        d.dateFrom = document.getElementById('dateFrom2').value;
                        d.port_origin = document.getElementById('port_origin2').value;
                        d.port_destination = document.getElementById('port_destination2').value;
                    },
                },


             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "created_on", "orderable": true, "className": "text-left"},
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
                    //console.log(allRow);
                    if(allRow.json.recordsTotal)
                    {
                        $('#download2').prop('disabled',false);
                    }
                    else
                    {
                        $('#download2').prop('disabled',true);
                    }
                }
            });

            $('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables2').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
            });
        },

        reload: function() {
            $('#dataTables2').DataTable().ajax.reload();
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };


    $(document).ready(function(){

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port_origin=$("#port_origin").val();
            var port_destination=$("#port_destination").val();
            var search= $('.dataTables_filter input').val();

            window.location.href="<?php echo site_url('transaction/syahbandarv2/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port_origin="+port_origin+"&port_destination="+port_destination+"&search="+search+"&status=0";
        });

        $("#download_excel2").click(function(event){
            var dateFrom2=$("#dateFrom2").val();
            var dateTo2=$("#dateTo2").val();
            var port_origin2=$("#port_origin2").val();
            var port_destination2=$("#port_destination2").val();
            var search2= $('#dataTables2_filter input').val();

            window.location.href="<?php echo site_url('transaction/syahbandarv2/download_excel?') ?>dateFrom="+dateFrom2+"&dateTo="+dateTo2+"&port_origin="+port_origin2+"&port_destination="+port_destination2+"&search="+search2+"&status=1";
        });

    });

</script>