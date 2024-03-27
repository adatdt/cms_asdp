<script type="text/javascript">
			class myData{
				loadData=()=> {
						$('#dataTables').DataTable({
								"ajax": {
										"url": "<?php echo site_url('laporan/menu_rekonsiliasi') ?>",
										"type": "POST",
										"data": function(d) {
												d.service = document.getElementById('service').value;
												d.dateFrom = document.getElementById('dateFrom').value;
												d.dateTo = document.getElementById('dateTo').value;
												d.dateFrom2 = document.getElementById('dateFrom2').value;
												d.dateTo2 = document.getElementById('dateTo2').value;
												d.dateFrom3 = document.getElementById('dateFrom3').value;
												d.dateTo3 = document.getElementById('dateTo3').value;
												d.merchant = document.getElementById('merchant').value;
												d.status_type = document.getElementById('status_type').value;
												d.searchName=$("#searchData").attr('data-name');
												d.searchData=document.getElementById('searchData').value;
												
										}                
								},
						
								"serverSide": true,
								"processing": true,
								"columns": [
												{"data": "no", "orderable": false, "className": "text-center" , "width": 5},
												{"data": "id_trans", "orderable": true, "className": "text-left"},
												{"data": "payment_code", "orderable": true, "className": "text-left"},
												{"data": "booking_code", "orderable": true, "className": "text-left"},
												{"data": "ticket_number", "orderable": true, "className": "text-left"},
												{"data": "merchant_id", "orderable": true, "className": "text-center"},
												{"data": "waktu_trans", "orderable": true, "className": "text-left"},
												{"data": "depart_date", "orderable": true, "className": "text-right"},
												{"data": "waktu_settle", "orderable": true, "className": "text-left"},
												{"data": "asal", "orderable": true, "className": "text-left"},
												{"data": "tujuan", "orderable": true, "className": "text-left"},
												{"data": "ship_class", "orderable": true, "className": "text-left"},
												{"data": "service", "orderable": true, "className": "text-left"},
												{"data": "golongan", "orderable": true, "className": "text-left"},
												{"data": "shop_code", "orderable": true, "className": "text-left"},
												{"data": "shop_name", "orderable": true, "className": "text-left"},
												{"data": "reconn_status", "orderable": true, "className": "text-center"},
												{"data": "tarif_per_jenis", "orderable": true, "className": "text-right"},
												{"data": "admin_fee", "orderable": true, "className": "text-right"},
												{"data": "diskon", "orderable": true, "className": "text-left"},
												{"data": "transfer_asdp", "orderable": true, "className": "text-left"},
												{"data": "code_promo", "orderable": true, "className": "text-left"},
												{"data": "updated_settlement", "orderable": true, "className": "text-left"},
												// {"data": "status_invoice", "orderable": true, "className": "text-left"},
												
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
								"order": [[ 0, "desc" ]],
								"initComplete": function () {
										var $searchInput = $('div.dataTables_filter input');
										var data_tables = $('#dataTables').DataTable();
										$searchInput.unbind();
										$searchInput.bind('keyup', function (e) {
												if (e.keyCode == 13 || e.whiche == 13) {
														data_tables.search(this.value).draw();
														totalrow();
												}
										});

								},

								fnDrawCallback: function(allRow)
								{   
										if(allRow.json.recordsTotal)
										{
												$('.download').prop('disabled',false);
										}
										else
										{
												$('.download').prop('disabled',true);
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
		}
</script>