<script type="text/javascript">
	class MyData{

        loadData=()=> {

            let urlData = "<?php echo site_url('transaction/invoice') ?>";
            // let urlData = "<?php //echo site_url('module_testing/invoice') ?>";

            $('#dataTables').DataTable({
                "ajax": {
                    "url": urlData,                    
                    "type": "POST",
                    "data": function(d) {
                        d.service = document.getElementById('service').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port_origin = document.getElementById('port_origin').value;
                        d.port_destination = document.getElementById('port_destination').value;
                        d.transaction_type = document.getElementById('transaction_type').value;
                        d.status_type = document.getElementById('status_type').value;
                        d.channel = document.getElementById('channel').value;
                        d.merchant = document.getElementById('merchant').value;
                        d.outletId = document.getElementById('outletId').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
                        // console.log(d.channel)
                    }
                },

                "serverSide": true,
                "processing": true,
                "columns": [{
                        "data": "no",
                        "orderable": false,
                        "className": "text-center",
                        "width": 5
                    },
                    {
                        "data": "created_on",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "trans_number",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "customer_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "phone_number",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "email",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "amount",
                        "orderable": false,
                        "className": "text-right"
                    },
                    {
                        "data": "service_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "port_origin",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "port_destination",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "channel",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "terminal_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "merchant_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "outlet_id",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "transaction_type_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "status_invoice",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "discount_code",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "description",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "due_date",
                        "orderable": false,
                        "className": "text-left"
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
                    if (allRow.json.recordsTotal) {
                        $('.download').prop('disabled', false);
                    } else {
                        $('.download').prop('disabled', true);
                    }
                }
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
        getOutletId(merchantId)
        {
            $.ajax({
                url: "<?php echo site_url('transaction/booking/getOutletId') ?>",
                type: "POST",
                dataType:"json",
                data: {
                    merchantId: merchantId
                },
                success: function(d) {
                    // console.log(d)
                    let merchant = $("#outletId"),
                        html = `<option value="">Pilih</option>`;

                    if (d.length > 0) {
                        for (var r = 0; r < d.length; r++) {
                            var res = d[r];
                            html += `<option value="${res.outlet_id}">${res.outlet_id}</option>`;
                        }
                    }
                    merchant.html(html);
                    $("#fOutletId").removeClass("hide");
                }
            })
        }        
	}
</script>