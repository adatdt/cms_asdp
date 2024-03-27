<script type="text/javascript">
    class MyData{
        loadData() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('pelabuhan/pcm_trx') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shipClass = document.getElementById('shipClass').value;
                        d.vehicleClass = document.getElementById('vehicleClass').value;
                        d.time = document.getElementById('time').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "depart_date", "orderable": true, "className": "text-left"},
                        {"data": "depart_time", "orderable": true, "className": "text-left"},
                        {"data": "quota", "orderable": true, "className": "text-right"},
                        {"data": "total_quota", "orderable": true, "className": "text-right"},
                        {"data": "used_quota", "orderable": true, "className": "text-right"},
                        // {"data": "status", "orderable": true, "className": "text-center"},
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

        reload () {
            $('#dataTables').DataTable().ajax.reload();
        }

        init () {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }

        estimation(){

            var totalQuota=$("#totalQuota").val();
            var quota=$("#quota").val()
            var action=$("#action").val()

            var a = totalQuota==""?0:parseInt(totalQuota);
            var b = quota==""?0:parseInt(quota);



            if(quota==0)
            {
                var c="";
            }
            else if(action==1)
            {
                var c= a+b;
            }
            else if (action==2)
            {
                var c= a-b;
            }
            else
            {
                var c="";
            }

            document.getElementById("estimation").value=c;

            console.log(c)
            console.log(a)
            console.log(b)
        }

    }

</script>

