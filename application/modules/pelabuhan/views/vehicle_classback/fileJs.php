<script type="text/javascript">
	class MyData{

        loadData() {

            var table = $('#dataTables');

            // begin first table
            table.dataTable({
                "ajax": {
                    "url": "<?php echo site_url('pelabuhan/vehicle_class') ?>",
                    "type": "POST",
                    "data": function (d) {}
                },
                "serverSide": true,
                "processing": true,
                "columns": [
                    {"data": "number", "orderable": false, "className": "text-center", "width": 20},
                    {"data": "name", "orderable": true, "className": "text-left"},
					{"data": "max_capacity", "orderable": true, "className": "text-right"},
                    {"data": "min_length", "orderable": true, "className": "text-right"},
					{"data": "max_length", "orderable": true, "className": "text-right"},
                    {"data": "default_weight", "orderable": true, "className": "text-right"},
                    {"data": "vehicle_type_name", "orderable": true, "className": "text-left"},
                    {"data": "group_vehicle_name", "orderable": true, "className": "text-left"},
                    {"data": "group_vehicle_type_name", "orderable": true, "className": "text-left"},
                    {"data": "description", "orderable": true, "className": "text-left"},
                    {"data": "wide_lm", "orderable": true, "className": "text-right"},
                    {"data": "length_lm", "orderable": true, "className": "text-right"},
                    {"data": "total_lm", "orderable": true, "className": "text-right"},
                     {"data": "img", "orderable": true, "className": "text-center"},
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

                "lengthMenu": [
                    [10, 15, 25, -1],
                    [10, 15, 25, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
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
                }
            });

        }

        init ()
        {
            if (!jQuery().dataTable) 
            { return; }

            this.loadData();
        }

        reload()
        {
	        $('#dataTables').DataTable().ajax.reload();
	    }

        numSparator(e)
        {
            if(e.which == 46){
                if($(this).val().indexOf('.') != -1) {
                    return false;
                }
            }

            if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        }

        countLinemeter(data)
        {

            if(data.wideLm=="" || data.wideLm==null )
            {
                var wide=0;
            }
            else
            {
                var wide=parseFloat(data.wideLm);   
            }

            if(data.lengthLm=="" || data.lengthLm==null )
            {
                var length=0;
            }
            else
            {
                var length=parseFloat(data.lengthLm);   
            }
            var luas = length * wide;
            
            return luas;            
            
        }            
	}


</script>