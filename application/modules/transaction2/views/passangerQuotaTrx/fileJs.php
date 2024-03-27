<script src="<?php echo base_url('assets/global/plugins/mergeDatatables.js'); ?>"></script>
<script type="text/javascript">
    class MyData {

        get loadData() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction2/passangerQuotaTrx') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo            = document.getElementById('dateTo').value;
                        d.dateFrom          = document.getElementById('dateFrom').value;
                        d.shipClass         = document.getElementById('shipClass').value;
                        d.port              = document.getElementById('port').value;
                        d.dataJam           = document.getElementById('dataJam').value;
                        // d.route             = document.getElementById('route').value;
                        // d.paymentDateFrom   = document.getElementById('paymentDateFrom').value;
                        // d.paymentDateTo     = document.getElementById('paymentDateTo').value;
                        // d.statusPP          = document.getElementById('statusPP').value;
                        // d.searchData        =document.getElementById('searchData').value;
                        // d.searchName        =$("#searchData").attr('data-name');
                    },
                },


                "serverSide": true,
                "processing": true,
                "columns": [{"data": "no","orderable": false,"className": "text-center","width": 5},
                            {"data": "depart_date","orderable": true,"className": "text-left"},
                            {"data": "depart_time","orderable": true,"className": "text-left"},
                            {"data": "port_name","orderable": true,"className": "text-left"},
                            {"data": "ship_class_name","orderable": true,"className": "text-left"},
                            {"data": "quota","orderable": true,"className": "text-right"},
                            {"data": "used_quota","orderable": true,"className": "text-right"},
                            {"data": "total_quota","orderable": true,"className": "text-right"},

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
                    // console.log(allRow);
                    if (allRow.json.recordsTotal) {
                        $('.download').prop('disabled', false);
                    } else {
                        $('.download').prop('disabled', true);
                    }
                }
            });

        }

        get reload() {
            $('#dataTables').DataTable().ajax.reload();
        }

        get init() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData;
        }

        route(data) {

            // console.log(data);
            $.ajax({

                type: "post",
                dataType: "json",
                url: "<?php echo site_url() ?>transaction2/vehicleTiketPP/getRoute",
                data: "port=" + data.port,
                success: (x) => {

                    var html = "<option value=''>Pilih</option>";

                    if (x.length > 0) {
                        for (var i = 0; i < x.length; i++) {
                            html += "<option value='" + x[i].id + "'>" + x[i].route_name + "</option>";
                        }

                    }

                    $("#route").html(html);
                }
            })
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