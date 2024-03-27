<script type="text/javascript">
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });


    class MyData  {

        loadData() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('device_management/mobileVersion') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.service = document.getElementById('service').value;
                    },
                },
             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "type", "orderable": true, "className": "text-left"},
                        {"data": "version", "orderable": true, "className": "text-left"},
                        {"data": "description", "orderable": true, "className": "text-left"},
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
                "fnDrawCallback": function(allRow) 
                {
                    let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                    let getToken = allRow.json[getTokenName];
                    csfrData[getTokenName] = getToken;

                    if( allRow.json[getTokenName] == undefined )
					{
						csfrData[allRow.json['csrfName']] = allRow.json['tokenHash'];
					}
                    
                    $.ajaxSetup({
                        data: csfrData
                    });
                }
            })
        }

        reload() {
            $('#dataTables').DataTable().ajax.reload();
        }

        init()
        {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    }

</script>