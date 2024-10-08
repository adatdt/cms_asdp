<script>
    class MyData{

        loadData() 
        {
            var table= $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data/pembatasanMemberB2b') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');                        
                    },
                },

                "serverSide": true,                
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "limit_transaction_code", "orderable": true, "className": "text-center"},
                        {"data": "merchant_name", "orderable": true, "className": "text-center"},
                        {"data": "start_date", "orderable": true, "className": "text-left"},
                        {"data": "end_date", "orderable": true, "className": "text-left"},
                        {"data": "limit_type", "orderable": true, "className": "text-center"},
                        {"data": "value", "orderable": true, "className": "text-center"},
                        {"data": "custom_type", "orderable": true, "className": "text-center","width": 5},                             
                        {"data": "custom_value", "orderable": true, "className": "text-center","width": 5},           
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
                        }
                    });
                },
            });

            $('#dataTables tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var td = $(this).closest('td');

                var row = table.row( tr );

        
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    td.html('<span  class="label label-success"><i class="fa fa-plus" aria-hidden="true"></i></span>');
                }
                else {
                    // Open this row
                    row.child( myData.format(row.data()) ).show();
                    tr.addClass('shown');  
                    td.html('<span  class="label label-danger"><i class="fa fa-minus" aria-hidden="true"></i></span>');                      
                    // myData.detailVehicle(row.data().id)
                    myData.detailMember(row.data().limit_transaction_code)                
                }                
                
            } );  
                                    
        }

        loadData_with_detail() 
        {
            var table= $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data/pembatasanMemberB2b') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');                        
                    },
                },

                "serverSide": true,                
                "processing": true,
                "columns": [
                            {
                            "className":      'details-control',
                            "orderable":      false,
                            "data":           null,
                            "defaultContent": '<span  class="label label-success"><i class="fa fa-plus" aria-hidden="true"></i></span>',
                            "targets": 0
                        },
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "limit_transaction_code", "orderable": true, "className": "text-left"},
                        {"data": "start_date", "orderable": true, "className": "text-left"},
                        {"data": "end_date", "orderable": true, "className": "text-left"},
                        {"data": "limit_type", "orderable": true, "className": "text-center"},
                        {"data": "value", "orderable": true, "className": "text-center"},
                        {"data": "custom_type", "orderable": true, "className": "text-center","width": 5},                             
                        {"data": "custom_value", "orderable": true, "className": "text-center","width": 5},           
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
                        }
                    });
                },
            });

            $('#dataTables tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var td = $(this).closest('td');

                var row = table.row( tr );

        
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    td.html('<span  class="label label-success"><i class="fa fa-plus" aria-hidden="true"></i></span>');
                }
                else {
                    // Open this row
                    row.child( myData.format(row.data()) ).show();
                    tr.addClass('shown');  
                    td.html('<span  class="label label-danger"><i class="fa fa-minus" aria-hidden="true"></i></span>');                      
                    // myData.detailVehicle(row.data().id)
                    myData.detailMember(row.data().limit_transaction_code)                
                }                
                
            } );  
                                    
        }        


        detailMember(limitTransactionCode) 
        {
            var table= $('#detailDataTables_'+limitTransactionCode).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data/pembatasanMemberB2b/getDetailMember') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.limitTransactionCode = limitTransactionCode;
                        d.searchData=document.getElementById(`searchData_${limitTransactionCode}`).value;
                        d.searchName=$(`#searchData_${limitTransactionCode}`).attr('data-name');                      
                    },
                },

                "serverSide": true,
                "processing": true,
                "autoWidth":false,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "email", "orderable": true, "className": "text-left","width": 5},
                        {"data": "limit_type", "orderable": true, "className": "text-left","width": 5}, 
                        {"data": "value", "orderable": true, "className": "text-left","width": 5},      
                        {"data": "custom_type", "orderable": true, "className": "text-center","width": 5},                                                 
                        {"data": "custom_value", "orderable": true, "className": "text-center","width": 5},           
                        {"data": "status", "orderable": true, "className": "text-center","width": 5},
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
                    var $searchInput = $('div #detailDataTables_'+limitTransactionCode+'_filter input');
                    var data_tables = $('#detailDataTables_'+limitTransactionCode).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });
                                    
        }        

        reload() 
        {
            $('#dataTables').DataTable().ajax.reload();
        }

        init() {
            if (!jQuery().DataTable) {
                return;
            }
            this.loadData();
        } 
        
        format ( d ) 
        {
            // console.log(d.add_detail_vehicle)
            var html = `<div style="background-color:#e1f0ff; padding:10px;">
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">Detail Pembatasan Member </div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">


                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">
                                            <ul class="nav nav-tabs " role="tablist">
                                                <li class="nav-item active">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab1_${d.limit_transaction_code}">Detail</a>
                                                </li>

                                            </ul>
                        
                                            <div class="tab-content " >

                                                <div class="tab-pane active" id="tab1_${d.limit_transaction_code}" role="tabpanel" >
                                                    

                                                    <div class="row">

                                                        <div class="col-sm-12 form-inline " align="left">
                                                            <div class="input-group pad-top">
                                                                <div class="input-group-btn">
                                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData__${d.limit_transaction_code}' >email                                            <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            <a href="javascript:;" onclick="myData.changeSearch('email','email')">email</a>
                                                                        </li>                                                                                                 
                                                                    </ul>
                                                                </div>
                                                                <!-- /btn-group -->
                                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="email" name="searchData_${d.limit_transaction_code}" id="searchData_${d.limit_transaction_code}"> 
                                                            </div>   
                                                            <div class="input-group pad-top">
                                                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" onClick="myData.cariDataDetail('${d.limit_transaction_code}')" id="cari_${d.limit_transaction_code}" >
                                                                    <span class="ladda-label">Cari</span>
                                                                    <span class="ladda-spinner"></span>
                                                                </button>
                                                            </div>  

                                                        </div>
                                                        <div class="col-md-12" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables_${d.limit_transaction_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Detail Pembatasan Member
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th> 
                                                                        <th>EMAIL</th>
                                                                        <th>TIPE PEMBATASAN</th>
                                                                        <th>VALUE</th>
                                                                        <th>CUSTOM</th> 
                                                                        <th>CUSTOM VALUE</th>     
                                                                        <th>STATUS</th>
                                                                        <th>
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            AKSI
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </th>
                                                                    </tr>
                                                                </thead>                                    
                                                            </table>      
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>      
                                        </div>
                                    </div>
                                                            
                                </div>
                            </div>            
            
                        </div>
            `
            // `d` is the original data object for the row
            return html;
        }  

        confirmationAction2(message, url, idTable) {
            alertify.confirm(message, function (e) {
                if (e) {
                    myData.returnConfirmation(url, idTable)
                }
            });
        }        
        
        returnConfirmation(url, idTable)
        {
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',

                beforeSend: function () {
                    $.blockUI({ message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>' });
                },

                success: function (json) {
                    if (json.code == 1) {
                        toastr.success(json.message, 'Sukses');
                        $(`#${idTable}`).DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function () {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function () {
                    $.unblockUI();
                }
            });
        }
        
	    changeSearch(x,name)
	    {
	    	$("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
	    	$("#searchData").attr('data-name', name);

	    }	
        
        getUser(data)
        {
            $.ajax({
                url:"<?= site_url() ?>master_data/pembatasanMember/getUser",
                type:"post",
                dataType:"json",
                success: function(x){

                    let getTd=``;

                    x.forEach(element => {
                        getTd +=`
                            <tr>
                                <td>${element.email}</td>
                            </tr>
                        `   
                    });

                    let html=`
                        <table class="table" id="tableUserLimit"></table>
                        <thead>
                            <tr>
                                <th>User / Email </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>tes </th>
                            </tr>
                            <tr>
                                <th>tes2 </th>
                            </tr>
                        </tbody>
                        </table>
                    
                    `;



                    $("#selectUser").html(html);

                }

            })   
        }  
        
        setDataTableClient(id)
        {
            $(`#${id}`).DataTable({
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "processing": "Proses.....",
                    "emptyTable": "Tidak ada data",
                    "info": " _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": " 0 sampai 0 dari 0 entri",
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
                // "searching": false,
                "pagingType": "bootstrap_full_number",
                // "order": [[ 0, "desc" ]],         
                "aoColumns": [
                { "bSortable": false },
                { "bSortable": false }
                // { "bSortable": true },
                // { "bSortable": true },
                // { "bSortable": true },
                // { "bSortable": true },
                // { "bSortable": false }
                ],
                "columnDefs": [
                                {
                                    "targets": [ 2 ],
                                    "visible": false                                    
                                },
                                {
                                    "targets": [ 3 ],
                                    "visible": false                                    
                                },                                
                            ]                        
            });
        }
        cariDataDetail(limitTransactionCode)
        {
            // alert(limitTransactionCode);
            

            $(`#cari_${limitTransactionCode}`).button('loading');
            $(`#detailDataTables_${limitTransactionCode}`).DataTable().ajax.reload();
            $(`#detailDataTables_${limitTransactionCode}`).on('draw.dt', function() {
                $(`#cari_${limitTransactionCode}`).button('reset');
            });
            

        }

        

    }
</script>