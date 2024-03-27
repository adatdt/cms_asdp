<script src="<?php echo base_url('assets/global/plugins/mergeDatatables.js'); ?>"></script>
<script type="text/javascript">
    class MyData {
        get loadData() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction2/passangerRefundTicket') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.shipClass = document.getElementById('shipClass').value;
                        d.port_origin = document.getElementById('port').value;
                        d.route = document.getElementById('route').value;
                        d.bank = document.getElementById('bank').value;
                        d.statusRefunded = document.getElementById('statusRefunded').value;
                        d.paymentDateFrom = document.getElementById('paymentDateFrom').value;
                        d.paymentDateTo = document.getElementById('paymentDateTo').value;
                    },
                    "dataSrc": function (json) {
                        if(json.data){
                            return json.data;
                        }
                        else{
                            logout();
                            return json;
                        }
                    },
                },
                "serverSide": true,
                "processing": true,
                "rowsGroup": [1,12,13,14,15,16,17],
                "columns": [{
                        "data": "no",
                        "orderable": false,
                        "className": "text-center",
                        "width": 5
                    },
                    {
                        "data": "booking_code",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "ticket_number",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "passanger_type_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "ship_class_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "fare",
                        "orderable": true,
                        "className": "text-right"
                    },
                    {
                        "data": "payment_date",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "depart_date",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "route_name",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "status_booking",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "status_refund",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "tanggal_approve",
                        "orderable": true,
                        "className": "text-left"
                    },
                    // {
                    //     "data": "check_in_time",
                    //     "orderable": true,
                    //     "className": "text-left"
                    // },
                    {
                        "data": "total_biaya",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "biaya_admin",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "biaya_refund",
                        "orderable": true,
                        "className": "text-left"
                    },
                    // {
                    //     "data": "boarding_time",
                    //     "orderable": true,
                    //     "className": "text-left"
                    // },
                    {
                        "data": "bank_tujuan",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "no_rekening",
                        "orderable": true,
                        "className": "text-left"
                    },
                    // {
                    //     "data": "boarding_time",
                    //     "orderable": true,
                    //     "className": "text-left"
                    // },
                    {
                        "data": "total_amount",
                        "orderable": true,
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
                url: "<?php echo site_url() ?>transaction2/passangerTicketRefund/getRoute",
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
    }
</script>