<script type="text/javascript">
	class MyData{

        loadData=()=> 
        {
            let urlData = "<?php echo site_url('transaction/booking') ?>";
            // let urlData = "<?php //echo site_url('module_testing/booking') ?>";

            $('#dataTables').DataTable({
                "ajax": {
                    "url": urlData,
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.service = document.getElementById('service').value;
                        d.port_origin = document.getElementById('port_origin').value;
                        d.port_destination = document.getElementById('port_destination').value;
                        d.depart_date = document.getElementById('depart_date').value;
                        d.channel = document.getElementById('channel').value;
                        d.merchant = document.getElementById('merchant').value;
                        d.status = document.getElementById('status').value;                                
                        d.searchData=document.getElementById('searchData').value;
                        d.keterangan = document.getElementById('keterangan').value; 
                        d.searchName=$("#searchData").attr('data-name');
                        const getOutlet= document.getElementById('outletId')
                        if(getOutlet)
                        {
                            d.outletId = getOutlet.value;
                        }                        
                    },
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
                        "data": "customer_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "service_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "trans_number",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "booking_code",
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
                        "data": "depart_date",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "created_on",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "total_passanger",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "amount",
                        "orderable": false,
                        "className": "text-right"
                    },
                    {
                        "data": "booking_channel",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "email",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "phone_number",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "card_no",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "terminal_code",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "terminal_name",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {
                        "data": "status",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {
                        "data": "keterangan",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "ref_no",
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
                        "data": "actions",
                        "orderable": false,
                        "className": "text-center"
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
                    // console.log(allRow);
                    if (allRow.json.recordsTotal) {
                        $('.download').prop('disabled', false);
                    } else {
                        $('.download').prop('disabled', true);
                    }
                }
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
        
        getOutletId =(merchantId)=>{
            $.ajax({
                url: "<?php echo site_url('transaction/booking/getOutletId') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    merchantId: merchantId,
                },
                success: function(data) {   
                    // console.log(data)                    
                    let selectData = `<option value="" >Pilih</option>`;
                    data.forEach(element => {
                        selectData += `<option value="${element.outlet_id}" >${element.outlet_id}</option>`;
                    });

                    const html =`                     
                        
                            <div class="input-group-addon">Outlet Id</div>
                            <select id="outletId" class="form-control select2 input-small" dir="" name="outletId">
                            ${selectData}
                            </select>
                    `

                    $("#fOutletId").html(html);
                    $('.select2').select2();
                }
            })
        }
	
	}
</script>