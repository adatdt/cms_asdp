<script>
    let arrayUserIdExcept=[]; //
    class MyData{

        loadData() 
        {
            var table= $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanTransaksi') ?>",
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
                        {"data": "jenis_pj", "orderable": true, "className": "text-left"},
                        {"data": "golongan", "orderable": true, "className": "text-left"},
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
                    myData.detailTransaksi(row.data().limit_transaction_code)        
                    myData.detailTransaksi2(row.data().limit_transaction_code)           
                    myData.getDetailTransaksiExcept(row.data().limit_transaction_code)                
                }                
                
            } );                                
        }     

        detailTransaksi(limitTransactionCode) 
        {
            var table= $('#detailDataTables_'+limitTransactionCode).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanTransaksi/getDetailTransaksi') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.limitTransactionCode = limitTransactionCode;
                        d.searchData=document.getElementById(`searchData_${limitTransactionCode}`).value;
                        d.searchName=$(`#searchData_${limitTransactionCode}`).attr('data-name');     
                        d.settingCustom="0";                    
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
                "searching": false,
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

        detailTransaksi2(limitTransactionCode) 
        {
            var table= $('#detailDataTables2_'+limitTransactionCode).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanTransaksi/getDetailTransaksi') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.limitTransactionCode = limitTransactionCode;
                        d.searchData=document.getElementById(`searchData_${limitTransactionCode}`).value;
                        d.searchName=$(`#searchData_${limitTransactionCode}`).attr('data-name');  
                        d.settingCustom="1";                 
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
                "searching": false,
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #detailDataTables2_'+limitTransactionCode+'_filter input');
                    var data_tables = $('#detailDataTables2_'+limitTransactionCode).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });
                                    
        }
        getDetailTransaksiExcept(limitTransactionCode) 
        {
            var table= $('#detailDataTables3_'+limitTransactionCode).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanTransaksi/getDetailTransaksiExcept') ?>",
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
                        {"data": "email", "orderable": true, "className": "text-left"}, 
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
                "searching":false,
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #detailDataTables3_'+limitTransactionCode+'_filter input');
                    var data_tables = $('#detailDataTables3_'+limitTransactionCode).DataTable();
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
                                    
                                    <div class="caption">Detail Pembatasan Transaksi </div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">


                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class=" form-inline " align="left">
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
                                        </div>
                                        <p></p>
                                        <div class="kt-portlet__body">
                                            <ul class="nav nav-tabs " role="tablist">
                                                <li class="nav-item active">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab1_${d.limit_transaction_code}">Pembatasan</a>
                                                </li>
                                                <li class="nav-item ">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab2_${d.limit_transaction_code}">Adjust Pembatasan</a>
                                                </li>                                                
                                                <li class="nav-item ">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab3_${d.limit_transaction_code}">Pengecualian</a>
                                                </li>

                                            </ul>
                        
                                            <div class="tab-content " >

                                                <div class="tab-pane active" id="tab1_${d.limit_transaction_code}" role="tabpanel" >
                                                    

                                                    <div class="row">

                                                        <div class="col-md-12" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables_${d.limit_transaction_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Detail Pembatasan Transaksi
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th> 
                                                                        <th>EMAIL USER</th>
                                                                        <th>RANGE WAKTU PEMBATASAN</th>
                                                                        <th>BATAS JUMLAH TRX</th>
                                                                        <th>CUSTOM RANGE WAKTU</th> 
                                                                        <th>CUSTOM NOMINAL <br> JENIS PEMBATASAN</th>     
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

                                                <div class="tab-pane " id="tab2_${d.limit_transaction_code}" role="tabpanel" >
                                                    

                                                    <div class="row">

                                                        <div class="col-md-12" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables2_${d.limit_transaction_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Detail Adjust Pembatasan Transaksi
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th> 
                                                                        <th>EMAIL USER</th>
                                                                        <th>RANGE WAKTU PEMBATASAN</th>
                                                                        <th>BATAS JUMLAH TRX</th>
                                                                        <th>CUSTOM RANGE WAKTU</th> 
                                                                        <th>CUSTOM NOMINAL <br> JENIS PEMBATASAN</th>     
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
                                                
                                                <div class="tab-pane " id="tab3_${d.limit_transaction_code}" role="tabpanel" >
                                                    

                                                    <div class="row">

                                                        <div class="col-md-12" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables3_${d.limit_transaction_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Detail Pengecualian Transaksi
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                            
                                                                        <th>NO</th> 
                                                                        <th>EMAIL USER</th>
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

        confirmationAction2(message, url, limitTransactionCode) {
            alertify.confirm(message, function (e) {
                if (e) {
                    myData.returnConfirmation(url, limitTransactionCode)
                }
            });
        }        
        
        returnConfirmation(url, limitTransactionCode)
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
                        // $(`#${idTable}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTables_${limitTransactionCode}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTables2_${limitTransactionCode}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTables3_${limitTransactionCode}`).DataTable().ajax.reload(null, false);
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
        
        getUser_lama(data)
        {
            $.ajax({
                url:"<?= site_url() ?>master_data/pembatasanTransaksi/getUser",
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
                "order": [[ 1, "desc" ]],         
                "aoColumns": [
                { "bSortable": false },
                { "bSortable": true }
                // { "bSortable": true },
                // { "bSortable": true },
                // { "bSortable": true },
                // { "bSortable": true },
                // { "bSortable": false }
                ]
                // ,
                // "columnDefs": [
                //                 {
                //                     "targets": [ 2 ],
                //                     "visible": false                                    
                //                 },
                //                 {
                //                     "targets": [ 3 ],
                //                     "visible": false                                    
                //                 },                                
                //             ]      
                ,
                "initComplete": function () {
                    var $searchInput = $('div #'+id+'_filter input');
                    var data_tables = $('#'+id).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });
        }
        cariDataDetail(limitTransactionCode)
        {
            // alert(limitTransactionCode);
            

            $(`#cari_${limitTransactionCode}`).button('loading');
            $(`#detailDataTables_${limitTransactionCode}`).DataTable().ajax.reload();
            $(`#detailDataTables2_${limitTransactionCode}`).DataTable().ajax.reload();
            $(`#detailDataTables3_${limitTransactionCode}`).DataTable().ajax.reload();
            $(`#detailDataTables_${limitTransactionCode}`).on('draw.dt', function() {
                $(`#cari_${limitTransactionCode}`).button('reset');
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

        tableUserLimited() 
        {

            $('#tableUserLimited').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanTransaksi/getUser') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idQuotaExcept = $("input[name='idQuotaExcept[]']").map(function(){return $(this).val();}).get();
                        d.idQuotaExcept = arrayUserIdExcept
                        d.idData = $("#idData").val();
                        // d.searchData=document.getElementById(`searchData_${limitTransactionCode}`).value;
                        // d.searchName=$(`#searchData_${limitTransactionCode}`).attr('data-name');                      
                    },
                },

                "serverSide": true,
                "processing": true,
                "autoWidth":false,
                "columns": [
                        {"data": "email", "orderable": true, "className": "text-left","width": 5},
                        {"data": "actions", "orderable": false, "className": "text-right"},
                ],
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
                "searching": true,
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #tableUserLimited_filter input');
                    var data_tables = $('#tableUserLimited').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                }
                ,

                // fnDrawCallback: function(allRow) {
                //     console.log(allRow);
                //     if (allRow.json.recordsTotal) {
                //         $('.btnPembatasan').prop('disabled', false);
                //     } else {
                //         $('.btnPembatasan').prop('disabled', true);
                //     }
                // }                
            });

                                    
        }

        reloadTableUserLimited() 
        {
            $('#tableUserLimited').DataTable().ajax.reload();            
                        
        }

        tableUserExcept() 
        {

            $('#tableUserLimitedExcept').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanTransaksi/getUserExcept') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idQuotaExcept = $("input[name='idQuotaExcept[]']").map(function(){return $(this).val();}).get();
                        d.idQuotaExcept = arrayUserIdExcept
                        d.idData = $("#idData").val()
                        // d.searchData=document.getElementById(`searchData_${limitTransactionCode}`).value;
                        // d.searchName=$(`#searchData_${limitTransactionCode}`).attr('data-name');                      
                    },
                },

                "serverSide": true,
                "processing": true,
                "autoWidth":false,
                "columns": [
                    {"data": "actions", "orderable": false, "className": "text-left"},
                    {"data": "email", "orderable": true, "className": "text-left","width": 5},
                ],
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
                "searching": true,
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #tableUserLimitedExcept_filter input');
                    var data_tables = $('#tableUserLimitedExcept').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });

                                    
        }

        reloadTableUserExcept() 
        {
            $('#tableUserLimitedExcept').DataTable().ajax.reload();            
                        
        }        

        toException(id,email)
        {
            let idData= $("#idData").val();
            if(idData==1)
            {

                let inputExceptUserDiv = `<input type="hidden" id="inputExceptUser_${id}" type='hidden' name='idQuotaExcept[${id}]' value='${id}' >`
                $("#inputExceptUserDiv").append(inputExceptUserDiv);
                
                let table2 = $("#tableUserLimitedExcept").DataTable();  
    
                const btn =`<div class='btn btn-danger transferDataLimit' title='Pindah Ke Pembatasan'  onClick=myData.toLimit('${id}') >
                                    <i class='fa fa-arrow-left' aria-hidden='true'></i>
                                </div>`  
    
                arrayUserIdExcept.push(id)      
            }            
            else
            {
                const filterData =arrayUserIdExcept.filter(
                    arrayUserIdExcept => arrayUserIdExcept != id
                );
    
                arrayUserIdExcept=[];
                arrayUserIdExcept=filterData;
    
                $(`#inputExceptUser_${id}`).remove();
            }
            
            /*
            let dataArrr=[btn, email]
            table2.row.add(dataArrr).draw().node();
            */

            this.reloadTableUserLimited(); 
            this.reloadTableUserExcept(); 

        }

        toLimit(id)
        {
            let idData= $("#idData").val();
            if(idData==1)
            {

                const filterData =arrayUserIdExcept.filter(
                    arrayUserIdExcept => arrayUserIdExcept != id
                );
    
                arrayUserIdExcept=[];
                arrayUserIdExcept=filterData;
    
                $(`#inputExceptUser_${id}`).remove();
                // console.log(arrayUserIdExcept); 
                // console.log(filterData); 
    
            }
            else
            {
                let inputExceptUserDiv = `<input type="hidden" id="inputExceptUser_${id}" type='hidden' name='idQuotaExcept[${id}]' value='${id}' >`
                $("#inputExceptUserDiv").append(inputExceptUserDiv);
                
                let table2 = $("#tableUserLimitedExcept").DataTable();  

                const btn =`<div class='btn btn-danger transferDataLimit' title='Pindah Ke Pembatasan'  onClick=myData.toLimit('${id}') >
                                    <i class='fa fa-arrow-left' aria-hidden='true'></i>
                                </div>`  

                arrayUserIdExcept.push(id)         
            }

                this.reloadTableUserLimited(); 
                this.reloadTableUserExcept(); 

        }        
 

    }
</script>