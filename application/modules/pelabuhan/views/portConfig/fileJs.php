<script>
    var csfrData = {};
        csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
        $.ajaxSetup({
            data: csfrData
        });

    class MyData{
        getTable() {
            var table = $('#dataTables');
            // begin first table
            table.dataTable({
                "ajax": {
                    "url": "<?php echo site_url('pelabuhan/portConfig') ?>",
                    "type": "POST",
                    "data": function (d) {
                            d.searchData=document.getElementById('searchData').value;
                            d.searchName=$("#searchData").attr('data-name');
                            d.port= $("#port").val();
                    },
                },

                "filter": false,
                "serverSide": true,
                "processing": true,
                "columns": [
                    {"data": "number", "orderable": false, "className": "text-center", "width": 20},
                    {"data": "config_name", "orderable": true, "className": "text-left"},
                    {"data": "config_group", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "value", "orderable": true, "className": "text-left"},
                    {"data": "status", "orderable": true, "className": "text-center"},
                    {"data": "actions", "orderable": false, "className": "text-center"}
                ],

                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
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

                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "lengthMenu": [
                    [10, 15, 25, -1],
                    [10, 15, 25, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                //           "columnDefs": [
                //               {
						// "targets": [1,2,3],
                //                   render: $.fn.dataTable.render.text()
                //               }
                //           ],
                "order": [
                    [0, "desc"]
                ], // set first column as a default sort by asc

                // users keypress on search data
                "initComplete": function () {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13) {
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
            });

        }

        init(){
            if (!jQuery().dataTable) {
                return;
            }
            this.getTable();
        }
        
        changeSearch(x,name)
        {
            $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
            $("#searchData").attr('data-name', name);

        }            
    }

</script>