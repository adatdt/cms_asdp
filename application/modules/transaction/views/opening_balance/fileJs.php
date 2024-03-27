<script type="text/javascript">
    var table2= {
        loadData: function() {
            $('#dataTables2').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/opening_balance/data_cs') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shift = document.getElementById('shift').value;
                    },
                },


                "serverSide": true,
                "processing": true,
                // "searching":false,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "assignment_date", "orderable": true, "className": "text-left"},
                    {"data": "shift_code", "orderable": true, "className": "text-left"},
                    {"data": "username", "orderable": true, "className": "text-left"},
                    {"data": "full_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-center"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "assignment_code", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": true, "className": "text-center"},
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
                "order": [[ 1, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #dataTables2_filter input');
                    var data_tables = $('#dataTables2').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },

                fnDrawCallback: function(allRow){
                    $('#searching').button('reset');
                    // console.log(allRow.json);
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

            // $('#export_tools > li > a.tool-action').on('click', function() {
            //     var data_tables = $('#dataTables').DataTable();
            //     var action = $(this).attr('data-action');

            //     data_tables.button(action).trigger();
            // });
        },

        reload: function() {
            $('#dataTables2').DataTable().ajax.reload(null, false);
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };

    var table3= {
        loadData: function() {
            $('#dataTables3').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/opening_balance/data_ptcstc') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shift = document.getElementById('shift').value;
                    },
                },


                "serverSide": true,
                "processing": true,
                // "searching":false,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "assignment_date", "orderable": true, "className": "text-left"},
                    {"data": "shift_code", "orderable": true, "className": "text-left"},
                    {"data": "username", "orderable": true, "className": "text-left"},
                    {"data": "full_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-center"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "assignment_code", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": true, "className": "text-center"},
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
                "order": [[ 1, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #dataTables3_filter input');
                    var data_tables = $('#dataTables3').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },

                fnDrawCallback: function(allRow){
                    $('#searching').button('reset');
                    // console.log(allRow.json);
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

            // $('#export_tools > li > a.tool-action').on('click', function() {
            //     var data_tables = $('#dataTables').DataTable();
            //     var action = $(this).attr('data-action');

            //     data_tables.button(action).trigger();
            // });
        },

        reload: function() {
            $('#dataTables3').DataTable().ajax.reload(null, false);
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    }; 

    var table4= {
        loadData: function() {
            $('#dataTables4').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/opening_balance/data_verifikator') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shift = document.getElementById('shift').value;
                    },
                },


                "serverSide": true,
                "processing": true,
                // "searching":false,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "assignment_date", "orderable": true, "className": "text-left"},
                    {"data": "shift_code", "orderable": true, "className": "text-left"},
                    {"data": "username", "orderable": true, "className": "text-left"},
                    {"data": "full_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-center"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "assignment_code", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": true, "className": "text-center"},
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
                "order": [[ 1, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #dataTables4_filter input');
                    var data_tables = $('#dataTables4').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },

                fnDrawCallback: function(allRow){
                    $('#searching').button('reset');
                    // console.log(allRow.json);
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

            // $('#export_tools > li > a.tool-action').on('click', function() {
            //     var data_tables = $('#dataTables').DataTable();
            //     var action = $(this).attr('data-action');

            //     data_tables.button(action).trigger();
            // });
        },

        reload: function() {
            $('#dataTables4').DataTable().ajax.reload(null, false);
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };   
    
    var table5= {
        loadData: function() {
            $('#dataTables5').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/opening_balance/data_comand_center') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shift = document.getElementById('shift').value;
                    },
                },


                "serverSide": true,
                "processing": true,
                // "searching":false,
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "assignment_date", "orderable": true, "className": "text-left"},
                    {"data": "shift_code", "orderable": true, "className": "text-left"},
                    {"data": "username", "orderable": true, "className": "text-left"},
                    {"data": "full_name", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-center"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "assignment_code", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": true, "className": "text-center"},
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
                "order": [[ 1, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #dataTables5_filter input');
                    var data_tables = $('#dataTables5').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },

                fnDrawCallback: function(allRow){
                    $('#searching').button('reset');
                    // console.log(allRow.json);
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

            // $('#export_tools > li > a.tool-action').on('click', function() {
            //     var data_tables = $('#dataTables').DataTable();
            //     var action = $(this).attr('data-action');

            //     data_tables.button(action).trigger();
            // });
        },

        reload: function() {
            $('#dataTables5').DataTable().ajax.reload(null, false);
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };

    function confAct(message,url){
	alertify.confirm(message, function (e) {
			if(e){

				var myStr = url;
        		var strArray = myStr.split("/");
/*
        		// console.log(strArray[8])
        		if(strArray['8']=='cs')
        		{
        			var no=2;
        		}
        		else
        		{
        			var no=3;	
        		}
*/
                switch (strArray[strArray.length - 1]) {
                    case 'cs':
                        var no=2;
                    break;
                    case 'ptcstc':
                        var no=3;
                    break;
                    case 'verifikator':
                        var no=4;
                    break;
                
                    default:
                        var no=5; // 5 usercomand
                        break;
                }

        		// console.log(no)
				returnConfirm(url,no)
			}
		});
	};

	function returnConfirm(url,no){
		$.ajax({
			url         : url,
			type        : 'GET',
			dataType    : 'json',

			beforeSend: function(){
				$.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});
			},

			success: function(json) {
				if(json.code == 1){

					var table="#dataTables"+no
					toastr.success(json.message, 'Sukses');
					$(table).DataTable().ajax.reload(null, false );
				}else{
					toastr.error(json.message, 'Gagal');
				}
			},

			error: function() {
				toastr.error('Silahkan Hubungi Administrator', 'Gagal');
			},

			complete: function(){
				$.unblockUI();
			}
		});
	}       
	

</script>