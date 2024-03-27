<script type="text/javascript">
    arrayUserIdImpact =[];
    class MyData{

        getFormVehicleClass(data, selectedVehicle=[])
        {
            const checked = [];
            selectedVehicle.forEach(arr => {
                checked[arr]="checked";
            });

            let option =`<li style="list-style-type: none;" ><input type="checkbox" class="allow" id="allDataCheck" data-checkbox="icheckbox_flat-grey" value="" >Semua &nbsp;&nbsp; </li>`;
            let i = 0;
            for (let x in data) {
                if( x !=""  )
                {
                    
                    option += `<li style="list-style-type: none;" ><div style="padding:5px;"></div>
                                    <input type="checkbox" class="allow check-vehicle-class" name='vehicleClass[${i}]' data-checkbox="icheckbox_flat-grey" value="${x}"  ${checked[x]}> ${data[x] } &nbsp;&nbsp; </li>`; 
                }
                
                i +=1;
            }

            let html =`
            <ul class="icheck-inline " style="display:none;" id="contentVehicleClass">
            
                    <label>Golongan <span class="wajib">*</span></label>                                
                    ${option}
            </ul> `;
            return html;
        }        
        format ( d ) 
        {
            // console.log(d.add_detail_vehicle)
            var html = `<div style="background-color:#e1f0ff; padding:10px;">
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">Detail RMS B2B</div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">


                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">

                                        </div>
                                        <p></p>
                                        <div class="kt-portlet__body">
                                            <ul class="nav nav-tabs " role="tablist">
                                                <li class="nav-item active">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab1_${d.id}">Merchant Berdampak</a>                                                
                                                </li>
                                                <li class="nav-item ">
                                                    <a class="label label-primary " data-toggle="tab" href="#tab2_${d.id}">Outlet Berdampak</a>                                                
                                                </li>
                                                <li class="nav-item ">
                                                    <a class="label label-primary " data-toggle="tab" href="#tab4_${d.id}">Outlet Dikecualikan</a>                                                
                                                </li>                                                                                                
                                                <li class="nav-item ">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab3_${d.id}">Golongan Kendaraan</a>
                                                </li>

                                            </ul>
                                            
                                            <div class="tab-content " >

                                                <div class="tab-pane active" id="tab1_${d.id}" role="tabpanel" >
                                                
                                                <div class="row">
                                                <div class="col-md-12" ></div>
                                                    <div class="col-md-6" >
                                                        <p></p>
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables2_${d.rms_code}" style=" width: 250px;">
                                                        <thead>
                                                                <tr>
                                                                    <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                        <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                            <div class="input-group select2-bootstrap-prepend">
                                                                                Merchant
                                                                            </div>

                                                                            <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                        <div>
                                                                    
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>NO</th> 
                                                                    <th>KODE RMS</th>
                                                                    <th>MERCHANT</th>
                                                                    <th>PEMBATASAN OUTLET</th>
                                                                </tr>
                                                            </thead>                                  
                                                        </table>      
                                                    </div>
                                                </div>

                                                </div>                    

                                                <div class="tab-pane " id="tab2_${d.id}" role="tabpanel" >
                                                <div class="row">
                                                    <div class="col-md-12"  style="color:red; font-style: italic; font-size:11px" align="left">
                                                        Detail outlet berdampak akan muncul aksi tambah outlet, jika Merchant sudah diceklis pembatasan outlet*
                                                        <br>
                                                        Aksi tidak akan tampil jika data induknya tidak aktif*
                                                     </div>                                                
                                                
                                                        <div class="col-md-6" align="right" >    
                                                            ${d.btn_add_detail_outlet}
                                                        </div>
                                                        <div class="col-md-12" > </div>
                                                        <div class="col-md-6" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables_${d.rms_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Outlet
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th>
                                                                        <th>KODE RMS</th> 
                                                                        <th>MERCHANT</th>
                                                                        <th>ID OUTLET</th>
                                                                        <th>KETERANGAN OUTLET</th>
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

                                                <div class="tab-pane " id="tab4_${d.id}" role="tabpanel" >
                                                <div class="row">
                                                    <div class="col-md-12"  style="color:red; font-style: italic; font-size:11px" align="left"></div>                                                
                                                
                                                        <div class="col-md-6" align="right" >    
                                                        </div>
                                                        <div class="col-md-12" > </div>
                                                        <div class="col-md-6" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables4_${d.rms_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Outlet
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th>
                                                                        <th>KODE RMS</th> 
                                                                        <th>MERCHANT</th>
                                                                        <th>ID OUTLET</th>
                                                                        <th>KETERANGAN OUTLET</th>
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

                                                
                                                <div class="tab-pane " id="tab3_${d.id}" role="tabpanel" >
                                                    

                                                    <div class="row">
                                                        <div class="col-md-12"  style="color:red; font-style: italic; font-size:11px;" align="left">
                                                        Jika Layanan kendaraan sebelumnya sudah di pilih atau diceklis,  kemudian di edit untuk tidak di pilih layanan kendaraanya,  maka data golongan  akan terupdate menjadi kosong*
                                                        </div>
                                                        <div class="col-md-6" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables3_${d.rms_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Golongan
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                            
                                                                        <th>NO</th> 
                                                                        <th> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            GOLONGAN
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                                        <!-- <th>STATUS</th>
                                                                        <th>
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            AKSI
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </th> -->
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
        getDataExeption(dataSet, user="web")
        {
            let email=[];
            
            let totalData = dataSet.size;
            let btn ="";
            let dataExeption =""
            let idExeption = user=="web"?"expWeb":"expIfcs"
            let splitString = "";
            let emailValue = "";
            let idValue = "";
            dataSet.forEach(element => {
                totalData -=1;                
                splitString = element.split("|");
                emailValue = splitString[0];
                idValue = splitString[1];
                if(user == "web")
                {                    
                    btn = `<a class="btn btn-danger " onclick=deleteUserWeb('${element}') title="hapus" ><i class="fa fa-trash-o"></i></a>`
                    dataExeption += `<input type="hidden" id="${element}" value="${idValue}" name="webExp[${totalData}]">`
                }
                else
                {
                    btn = `<a class="btn btn-danger " onclick=deleteUserIfcs('${element}') title="hapus"><i class="fa fa-trash-o"></i></a>`
                    dataExeption += `<input type="hidden" id="${element}" value="${idValue}" name="webExp[${totalData}]">`
                }
                // ini untuk set data di datatable
                email[totalData] = ["", emailValue,btn];         
                                
            });

            $(`#${idExeption}`).html(dataExeption);
            return email;
        }  
        detailOutlet(data)
        {
            const getTable = $(`#detailDataTables_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms_b2b/detailOutlet') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.rmsCode = data.rms_code_enc;
                        // d.team = document.getElementById('team').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "rms_code", "orderable": true, "className": "text-left"},
                        {"data": "merchant_name", "orderable": true, "className": "text-left"},
                        {"data": "outlet_id", "orderable": true, "className": "text-left"},
                        {"data": "description", "orderable": false, "className": "text-left"},
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
                    var $searchInput = $(`div#detailDataTables_${data.rms_code}_filter input`);
                    var data_tables = $(`#detailDataTables_${data.rms_code}`).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                "fnDrawCallback": function(allRow) 
                {
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
        }   
        detailOutletExcept(data)
        {
            const getTable = $(`#detailDataTables4_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms_b2b/detailOutletExcept') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.rmsCode = data.rms_code_enc;
                        // d.team = document.getElementById('team').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "rms_code", "orderable": true, "className": "text-left"},
                        {"data": "merchant_name", "orderable": true, "className": "text-left"},
                        {"data": "outlet_id", "orderable": true, "className": "text-left"},
                        {"data": "description", "orderable": false, "className": "text-left"},
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
                    var $searchInput = $(`div#detailDataTables4_${data.rms_code}_filter input`);
                    var data_tables = $(`#detailDataTables4_${data.rms_code}`).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                "fnDrawCallback": function(allRow) 
                {
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
        }             
        detailMerchant(data)
        {
            const getTable = $(`#detailDataTables2_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms_b2b/detailMerchant') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.rmsCode = data.rms_code_enc;
                        // d.team = document.getElementById('team').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "rms_code", "orderable": false, "className": "text-left"},
                        {"data": "merchant_name", "orderable": true, "className": "text-center"},
                        {"data": "is_outlet", "orderable": false, "className": "text-center"},
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
                    var $searchInput = $(`div#detailDataTables2_${data.rms_code}_filter input`);
                    var data_tables = $(`#detailDataTables2_${data.rms_code}`).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                "fnDrawCallback": function(allRow) 
                {
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
        }                          
        detailGolongan(data)
        {
            const getTable = $(`#detailDataTables3_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/detailGolongan') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.rmsCode = data.rms_code_enc;
                        // d.team = document.getElementById('team').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                        // {"data": "actions", "orderable": false, "className": "text-center"},
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
                "order": [[ 0, "asc" ]],
                "initComplete": function () {
                    var $searchInput = $(`div#detailDataTables3_${data.rms_code}_filter input`);
                    var data_tables = $(`#detailDataTables3_${data.rms_code}`).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
                "fnDrawCallback": function(allRow) 
                {
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
        }                
        confirmationAction(message, url, tab="") {
            alertify.confirm(message, function (e) {
                if (e) {
                    myData.returnConfirmation(url, tab)
                }
            });
        }
        returnConfirmation(url, tab)
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
                            // $(`#detailDataTables${tab}_${json.data['rms_code']}`).DataTable().ajax.reload(null, false);
                            $(`#detailDataTables_${json.data['rms_code']}`).DataTable().ajax.reload(null, false);
                            $(`#detailDataTables4_${json.data['rms_code']}`).DataTable().ajax.reload(null, false);

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

        dataInitEdit(selectedM, isOutletM, setDataChecked, getDataMerchant)
        {
            // set selected merchant
            // const  selectedMerchant =`<implode(",",$selectedMerchant); ?>`.split(",");
            const  selectedMerchant =selectedM.split(",");
            $('#getMerchant').val(selectedMerchant).change();

            // const getIsOutlet =  JSON.parse(`= json_encode($isOutlet); ?>`);
            const getIsOutlet =  JSON.parse(isOutletM);
            getIsOutlet.forEach(element => {
                setDataChecked.add(element);
            });

            let getText = $("#getMerchant").select2('data')    
            let dataSelected = getDataMerchant;
            getDataMerchant = [];
            let ischeck ="";
            let idChecked ="";
            getText.forEach(element => {
                ischeck ="";
                idChecked ="";
                setDataChecked.forEach(x => {
                    if(element.id == x)
                    {
                        ischeck ="checked";
                        idChecked =x;
                    }    
                });

                let hapus = `<a class="btn btn-danger hps" data-id='${element.id}'   id='hps_${element.text}'   data-text='${element.text}'  title="hapus" ><i class="fa fa-trash-o"></i></a>`
                let checked = `<input type="checkbox"  class="isChecked"  data-id="${element.text}" data-checkbox="icheckbox_flat-grey" value="${element.id}" ${ischeck} >`;
                getDataMerchant.push(["",element.text, checked, hapus,idChecked]); 
            });

            // set checked outlet
            let isOutlet =[]; 
                setDataChecked.forEach(element => {
                isOutlet.push(element)
                })
            // console.log(isOutlet);
            $(`#isOutlet`).val(isOutlet.toString())                
            
            return [setDataChecked,getDataMerchant];

        }

        showModal(lat, long, rmsCode)
        {
            $('#modalMap').modal('show');
            // console.log(lat)
            // console.log(`view_${rmsCode}`)
            const radius = $(`#view_${rmsCode}`).attr("data-radius");
            const radiusType =  $(`#view_${rmsCode}`).attr("data-radiusType");
            const html = `

                                        KODE RMS B2B: ${rmsCode} <br>
                                        RADIUS : ${radius} ${radiusType}<br>
                                        LONG : ${long}<br>
                                        LAT : ${lat} <br> `;
            $("#infoView").html(html);
            $(`#rmsCodeView`).val(rmsCode);

            const dataRadius = {radius: radius, radiusType: radiusType};
            initMapView(lat, long, dataRadius );
        }
        tableUserLimited() 
        {

            $('#tableUserLimited').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms_b2b/getOutletImpact') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idQuotaExcept = $("input[name='idQuotaExcept[]']").map(function(){return $(this).val();}).get();
                        d.idMemberImpact = arrayUserIdImpact
                        d.idData = $("#idData").val();
                        d.rmsCode = $("#rmsCode").val();
                        // d.searchData=document.getElementById(`searchData_${limitTransactionCode}`).value;
                        // d.searchName=$(`#searchData_${limitTransactionCode}`).attr('data-name');                      
                    },
                },

                "serverSide": true,
                "processing": true,
                "autoWidth":false,
                "columns": [
                    {"data": "merchant_name", "orderable": true, "className": "text-left","width": 5},
                        {"data": "outlet_id", "orderable": true, "className": "text-left","width": 5},
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
                    "url": "<?php echo site_url('radius/rms_b2b/getOutletExcept') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idQuotaExcept = $("input[name='idQuotaExcept[]']").map(function(){return $(this).val();}).get();
                        d.idMemberImpact = arrayUserIdImpact
                        d.idData = $("#idData").val()
                        d.rmsCode = $("#rmsCode").val();
                        // d.searchData=document.getElementById(`searchData_${limitTransactionCode}`).value;
                        // d.searchName=$(`#searchData_${limitTransactionCode}`).attr('data-name');                      
                    },
                },

                "serverSide": true,
                "processing": true,
                "autoWidth":false,
                "columns": [
                    {"data": "actions", "orderable": false, "className": "text-left"},
                    {"data": "outlet_id", "orderable": true, "className": "text-left","width": 5},
                    {"data": "merchant_name", "orderable": true, "className": "text-left","width": 5},
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

                const filterData =arrayUserIdImpact.filter(
                    arrayUserIdImpact => arrayUserIdImpact != id
                );
    
                arrayUserIdImpact=[];
                arrayUserIdImpact=filterData;
    
                $(`#inputImpactUser_${id}`).remove();
            }            
            else 
            {
                let inputImpactUserDiv = `<input type="hidden" id="inputImpactUser_${id}" type='hidden' name='idMemberImpact[${id}]' value='${id}' >`
                $("#inputImpactUserDiv").append(inputImpactUserDiv);
                
                let table2 = $("#tableUserLimitedExcept").DataTable();  
    
                const btn =`<div class='btn btn-danger transferDataLimit' title='Pindah Ke Pembatasan'  onClick=myData.toLimit('${id}') >
                                    <i class='fa fa-arrow-left' aria-hidden='true'></i>
                                </div>`  
    
                arrayUserIdImpact.push(id)                      

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
            if(idData==1 )
            {

                // console.log(arrayUserIdImpact); 
                // console.log(filterData); 

                let inputImpactUserDiv = `<input type="hidden" id="inputImpactUser_${id}" type='hidden' name='idMemberImpact[${id}]' value='${id}' >`
                $("#inputImpactUserDiv").append(inputImpactUserDiv);
                
                let table2 = $("#tableUserLimitedExcept").DataTable();  

                const btn =`<div class='btn btn-danger transferDataLimit' title='Pindah Ke Pembatasan'  onClick=myData.toLimit('${id}') >
                                    <i class='fa fa-arrow-left' aria-hidden='true'></i>
                                </div>`  

                arrayUserIdImpact.push(id)     
                console.log(arrayUserIdImpact) ;

    
            }
            else if (idData==0)
            {

                const filterData =arrayUserIdImpact.filter(
                    arrayUserIdImpact => arrayUserIdImpact != id
                );
    
                arrayUserIdImpact=[];
                arrayUserIdImpact=filterData;
    
                $(`#inputImpactUser_${id}`).remove();                
   
            }

                this.reloadTableUserLimited(); 
                this.reloadTableUserExcept(); 

        }            
        
    }

</script>