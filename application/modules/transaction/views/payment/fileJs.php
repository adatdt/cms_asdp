<script type="text/javascript">
	class MyData
	{
        loadData=()=> {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/payment') ?>",
                    "type": "POST",
                    // "timeout": 60000,
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.service = document.getElementById('service').value;
                        d.port = document.getElementById('port').value;
                        d.shift = document.getElementById('shift').value;
                        d.due_date = document.getElementById('due_date').value;
                        d.channel = document.getElementById('channel').value;
                        d.sofId = document.getElementById('sofId').value;
                        d.merchant = document.getElementById('merchant').value;
                        d.outletId = $("#outletId").val();
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
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
                        "data": "payment_date",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "created_on",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "booking_code",
                        "orderable": false,
                        "className": "text-center"
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
                    // {"data": "due_date", "orderable": true, "className": "text-left"},
                    {
                        "data": "invoice_date",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "payment_type",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "merchant_name",
                        "orderable": false,
                        "className": "text-left"
                    },                    
                    {
                        "data": "amount",
                        "orderable": false,
                        "className": "text-right"
                    },
                    {
                        "data": "amount_invoice",
                        "orderable": false,
                        "className": "text-right"
                    },
                    {
                        "data": "admin_fee",
                        "orderable": false,
                        "className": "text-right"
                    },                                        
                    {
                        "data": "service_name",
                        "orderable": false,
                        "className": "text-right"
                    },
                    // {"data": "total_amount", "orderable": true, "className": "text-right"},
                    {
                        "data": "channel",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "outlet_id",
                        "orderable": false,
                        "className": "text-left"
                    },                    
                    {
                        "data": "shift_name",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "origin",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "destination",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "depart_date",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "depart_time",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "tipe_transaksi",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "trans_code",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "card_no",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "sof_id",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "discount_code",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "description",
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "ref_no",
                        "orderable": false,
                        "className": "text-left"
                    },
                    {
                        "data": "actions", 
                        "orderable": false, 
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
                footerCallback: function(row, data, start, end, display) {

                    // console.log(data);
                    if( data.length > 0)
	                {
	                    $('.download').prop('disabled',false);
	                }
	                else
	                {
	                    $('.download').prop('disabled',true);
	                }

                    var api = this.api()                    
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    // Total over this page
                    let pageTotal = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {

                            var c = intVal(a) + intVal(b.replace(".", ""));
                            return parseInt(c)

                        }, 0);

                    var myData= new MyData()
                    $(api.column(0).footer()).html(myData.numberFormat("Nominal Transaksi (Rp) : <span id='allTotalPage'></span>"));


                },
                fnDrawCallback: function(allRow) {
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

                	var myData= new MyData()
                    // console.log(data.json.sumData)
                    $("#allTotalPage").html(" " + allRow.json.dataAmountPage + " ( dari " + myData.numberFormat(allRow.json.sumData) + " )");
                }
            });

        }

        reload=()=>
        {

            $('#dataTables').DataTable().ajax.reload();

        }

        init=()=> {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }

	    numberFormat=(x)=> {
	        if (x == "" || x == null) {
	            return 0;
	        } else {
	            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	        }
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
        

	    changeSearch=(x,name)=>
	    {
	    	$("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
	    	$("#searchData").attr('data-name', name);

	    }	
        getOutletId(merchantId)
        {
            $.ajax({
                url: "<?php echo site_url('transaction/payment/getOutletId') ?>",
                type: "POST",
                dataType: 'json',
                data: {
                    merchantId: merchantId
                },
                success: function(json) {
                    // var d = JSON.parse(data),
                    
                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });

                    var d = json.data,
                        merchant = $("#outletId"),
                        html = '';

                    if (d.length > 0) {
                        for (var r = 0; r < d.length; r++) {
                            var res = d[r];
                            html += `<option value="${res.id}">${res.name}</option>`;
                        }
                    }
                    merchant.html(html);
                }
            })
        }


	}
</script>