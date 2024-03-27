<script type="text/javascript">
	
	class MyData{

		loadData=()=> {


			$('#dataTables').DataTable({
				"ajax": {
	                "url": "<?php echo site_url('refund') ?>",
	                "type": "POST",
	                "data": function(d) {
	                   	d.dateFrom 		= $("#dateFrom").val();
	                    d.dateTo 		= $("#dateTo").val();
	                    d.port 			= $("#port").val();
	                    d.refund_type 	= $("#refund_type").val();
	                    d.ship_class 	= $("#ship_class").val();
	                    d.status 		= $("#status").val();
	                    d.approvedBy 	= $("#approvedBy").val();
	                    d.sla		 	= $("#sla").val();
                        d.searchData	= document.getElementById('searchData').value;
                        d.searchName	= $("#searchData").attr('data-name');	                    
	                },
	            },
			 
	            "serverSide": true,
	            "processing": true,
	                  "columns": [
	                    // {"data": "checkBox", "orderable": false, "className": "text-center"},
	                    // {"data": "number", "orderable": false, "className": "text-center"},
	                    // {"data": "booking_code", "orderable": true, "className": "text-left"},
						// {"data": "name", "orderable": true, "className": "text-left"},
	                    // {"data": "phone", "orderable": true, "className": "text-left"},
	                    // {"data": "created_on", "orderable": true, "className": "text-left"},
	                    // {"data": "refund_code", "orderable": true, "className": "text-left"},
	                    // {"data": "port_name", "orderable": true, "className": "text-left"},
	                    // {"data": "route_name", "orderable": true, "className": "text-left"},
	                    // {"data": "service_name", "orderable": true, "className": "text-left"},
	                    // {"data": "account_number", "orderable": true, "className": "text-left"},
	                    // {"data": "account_name", "orderable": true, "className": "text-left"},
	                    // {"data": "bank", "orderable": true, "className": "text-left"},
	                    // {"data": "total_amount", "orderable": true, "className": "text-right"},
	                    // {"data": "status", "orderable": true, "className": "text-center"},
	                    // {"data": "status_approved", "orderable": true, "className": "text-center"},
	                    // {"data": "approved_by", "orderable": true, "className": "text-left"},
	                    // {"data": "approved_on", "orderable": true, "className": "text-left"},
	                    // {"data": "channel", "orderable": true, "className": "text-center"},
	                    // {"data": "actions", "orderable": false, "className": "text-center"}				

	                    {"data": "checkBox", "orderable": false, "className": "text-center"},
	                    {"data": "icon_sla", "orderable": false, "className": "text-center"},
						{"data": "number", "orderable": false, "className": "text-center"},
	                    {"data": "booking_code", "orderable": false, "className": "text-center"},
	                    {"data": "name", "orderable": true, "className": "text-left"},
						{"data": "phone", "orderable": true, "className": "text-left"},
						{"data": "created_on", "orderable": true, "className": "text-left"},
						{"data": "refund_code", "orderable": true, "className": "text-left"},
						{"data": "refund_type", "orderable": true, "className": "text-left"},
						{"data": "asal", "orderable": true, "className": "text-left"},
						{"data": "tujuan", "orderable": true, "className": "text-left"},
						{"data": "layanan", "orderable": true, "className": "text-left"},
						{"data": "jenis_pj", "orderable": true, "className": "text-left"},
						{"data": "id_number", "orderable": true, "className": "text-left"},
						{"data": "golongan", "orderable": true, "className": "text-left"},
						{"data": "account_number", "orderable": true, "className": "text-left"},
						{"data": "account_name", "orderable": true, "className": "text-left"},
						{"data": "bank", "orderable": true, "className": "text-left"},
						{"data": "amount", "orderable": true, "className": "text-left"},
						{"data": "adm_fee", "orderable": true, "className": "text-left"},
						{"data": "refund_fee", "orderable": true, "className": "text-left"},
						{"data": "bank_transfer_fee", "orderable": true, "className": "text-left"},
						{"data": "jumlah_potongan", "orderable": true, "className": "text-left"},
						{"data": "dana_pengembalian", "orderable": true, "className": "text-left"},
						{"data": "status_refund", "orderable": true, "className": "text-center"},
						//cs/cc
						{"data": "approved_status_cs", "orderable": true, "className": "text-center  ccCell"},
						{"data": "approved_by_cs", "orderable": true, "className": "text-center ccCell"},
						{"data": "approved_on_cs", "orderable": true, "className": "text-center ccCell"},
						{"data": "sla_cs", "orderable": true, "className": "text-center ccCell"},
						{"data": "keterangan_cs", "orderable": true, "className": "text-center ccCell"},
						{"data": "catatan_cs", "orderable": true, "className": "text-center ccCell"},		
						// usaha
						{"data": "status_approved", "orderable": true, "className": "text-center usahaCell "},
						{"data": "approved_by", "orderable": true, "className": "text-center usahaCell"},
						{"data": "approved_on", "orderable": true, "className": "text-center usahaCell"},
						{"data": "sla_usaha", "orderable": true, "className": "text-center usahaCell"},
						{"data": "keterangan_usaha", "orderable": true, "className": "text-center usahaCell"},
						{"data": "catatan_usaha", "orderable": true, "className": "text-center usahaCell"},
						// keuangan
						{"data": "status_keuangan", "orderable": true, "className": "text-center keuanganCell"},
						{"data": "approved_by_keuangan", "orderable": true, "className": "text-center keuanganCell"},
						{"data": "approved_on_keuangan", "orderable": true, "className": "text-center keuanganCell"},
						{"data": "sla_keuangan", "orderable": true, "className": "text-center keuanganCell"},						
						{"data": "keterangan_keuangan", "orderable": true, "className": "text-center keuanganCell"},
						{"data": "catatan_keuangan", "orderable": true, "className": "text-center keuanganCell"},

						{"data": "durasi", "orderable": true, "className": "text-center"},
						{"data": "keterangan", "orderable": true, "className": "text-center"},
	                    {"data": "actions", "orderable": false, "className": "text-center"}						
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
		        // scrollX:        "hidden",
				// scrollY:        "500px",
		        // paging:         false,
		        // fixedColumns:   {
		        //     leftColumns: 5,
		        //     // left: 1,
		        //     // right: 1
		        // }, 
		        
	            "pageLength": 10,
	            "searching":false,
	            "bSortClasses": false,
	            "highlight":false,
	            "pagingType": "bootstrap_full_number",
	            "order": [[ 2, "desc" ]],
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
	            fnDrawCallback: function(allRow)
	            {
	                // console.log(allRow);

                    if (allRow.json.recordsTotal>0) 
                    {
                        $('.download').prop('disabled', false);
                    } 
                    else 
                    {
                        $('.download').prop('disabled', true);
                    }

	                if(allRow.json.recordsTotal)
	                {
	                    $('.check').addClass( "icheck" );
	                }

					// $("td.usahaCell").attr('style', 'background-color: #cbeded');
					// $("td.keuanganCell").attr('style', 'background-color: #d0fbc2');
					// $("td.ccCell").attr('style', 'background-color: #fafba6');

					/*
					$(".ccCell").attr('style', 'background-color: #ffff6e');
					$(".usahaCell").attr('style', 'background-color: #ffed95');
					$(".keuanganCell").attr('style', 'background-color: #ffbc8d');
					*/


	                for (var i=0; i<allRow.json.idThProsesCs.length; i++)
	                {
	                	var idTh="#"+allRow.json.idThProsesCs[i];
	                	// var idThHover=".table-hover>tbody>#"+allRow.json.idThProsesCs[i]+":hover, .table-hover>tbody>#"+allRow.json.idThProsesCs[i]+":hover>td";

	                	var idThHover="table.dataTable tbody tr:hover"

	                	// console.log(idThHover)
	                	$(idTh).attr('style', 'background-color: #fff2f7 !important');

						$( `${idTh} .usahaCell`).attr('style', 'background-color: #fff2f7');
						$( `${idTh} .keuanganCell`).attr('style', 'background-color: #fff2f7');
						$( `${idTh} .ccCell`).attr('style', 'background-color: #fff2f7');

	                	// $(` ${idTh} td.sorting_1:hover`).attr('style', 'background-color:#f1e1de !important ')
	                	// $(idThHover).attr('style', 'background-color:green !important');

	                	// #f3f4f6
						
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
			if (!jQuery().DataTable) 
			{
	            return;
	        }

	        this.loadData();
		}
		approveData=()=>
		{
		    var idApprove=[];
		    $('input.myCheck:checkbox:checked').each(function () {
		        idApprove.push($(this).val());
		    });

		    var l = Ladda.create(document.querySelector('.ladda-button'));

		    alertify.confirm("Apakah anda yakin ingin approve data ini", function (e) {
		        if(e)
		        {
		            $.ajax({
		                dataType : "JSON",
		                type : "post",
		                url : "<?php echo site_url()?>refund/refund/actionApprove",
		                data :{idApprove:idApprove},
		                beforeSend: ()=>{
		                    l.start();
		                    unBlockUiId("dataTables");
		                },
		                success : (x)=>{
		                    
		                    if(x.code==1)
		                    {
		                        toastr.success(x.message, 'Sukses');
		                        $('#dataTables').DataTable().ajax.reload( null, false );
		                    }
		                    else
		                    {
		                        toastr.error(x.message, 'Gagal');
		                    }
		                },
		                error: ()=> {
		                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
		                },
		                complete: function(){
		                     l.stop();
		                     $('#dataTables').unblock(); 
		                }                
		            })
		        }
		    });
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

	}

</script>