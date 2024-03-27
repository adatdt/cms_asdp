<script type="text/javascript">
    class MyData {

        loadData = () => {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('log/portConfigLog') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port = document.getElementById('port').value;
                        d.shipClass=document.getElementById('shipClass').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name')
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                    {
                        "data": "no",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "created_on",
                        "orderable": true,
                        "className": "text-left"
                    },                    
                    {
                        "data": "pcis_code",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "port_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "ship_class_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {                    
                        "data": "request",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {                    
                        "data": "response",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {                    
                        "data": "type",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {                    
                        "data": "description",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {                    
                        "data": "status",
                        "orderable": true,
                        "className": "text-center"
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
                // "pageLength": 10,
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
                }
            });

        }

        reload = () => {
            $('#dataTables').DataTable().ajax.reload();
        }

        init = () => {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
        formatDate = (date) => {
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


        changeSearch(x, name) {
            $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
            $("#searchData").attr('data-name', name);

        }

    }
</script>