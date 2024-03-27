<script type="text/javascript">
    class MyData{
        loadData() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('pelabuhan/pcm_trx_global') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shipClass = document.getElementById('shipClass').value;
                        d.time = document.getElementById('time').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "depart_date", "orderable": true, "className": "text-left"},
                        {"data": "depart_time", "orderable": true, "className": "text-left"},
                        {"data": "quota", "orderable": true, "className": "text-right"},
                        {"data": "total_quota", "orderable": true, "className": "text-right"},
                        {"data": "used_quota", "orderable": true, "className": "text-right"},
                        {"data": "quota_reserved", "orderable": true, "className": "text-right"},
                        {"data": "total_lm", "orderable": false, "className": "text-right"},
                        {"data": "lmTersedia", "orderable": false, "className": "text-right"},
                        {"data": "lmDigunakan", "orderable": false, "className": "text-right"},
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
                var c=totalQuota;
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

        }

        directUrl(url)
        {
            window.location.href=`${url}`;
        }
        postData2(url,data)
        {
            $.ajax({
                url         : url,
                data        : new FormData($('form')[0]),
                type        : 'POST',
                dataType    : 'json',
                contentType :false,
                processData :false,


                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1){
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        $('#dataTables').DataTable().ajax.reload();

                        console.log(json.data);
                        // jika minta list mana aja yang tidak ke update
                        var arr=json.data;
                        if (arr.length > 0)
                        {
                            showModal('pcm_trx_global/listErr');
                        }
                    }
                    else
                    {
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){
                    $('#box').unblock(); 
                }
            });
        }

    }

</script>

