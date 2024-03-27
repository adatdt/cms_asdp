<script type="text/javascript">
    class MyData {

        loadData = () => {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('log/cmsCiLog') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        // d.searchData=document.getElementById('searchData').value;
                        // d.searchName=$("#searchData").attr('data-name');
                    },
                },

                "serverSide": false,
                "processing": true,
                "columns": [{
                        "data": "date",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {
                        "data": "level",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {
                        "data": "content",
                        "orderable": true,
                        "className": "text-left"
                    }
                ],
                "columnDefs": [{
                        "render": function(data, type, row) {
                            return '<span class="text-' + row['class'] + '">' + ' <span class="' + row['icon'] + '" aria-hidden="true" style="margin-right:4px"></span>' + data + ' </span>';
                        },
                        "targets": 1
                    },
                    {
                        "render": function(data, type, row, meta) {
                            let expand = '';
                            let extra = '';

                            if (row['extra']) {
                                expand += '<a class="pull-right expand btn btn-success btn-xs" data-display="stack' + meta.row + '">\
                                        <span class="fa fa-search"></span>\
                                    </a>';
                                extra += ' <div class="stack" id="stack' + meta.row + '" style = "display: none; white-space: pre-wrap;">' + row['extra'] + '</div>'
                            }

                            return '<pre>' + expand + data.replace(/\n/g, "") + ' ' + extra + '</pre>';
                        },
                        "targets": 2
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
                "searching": true,
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