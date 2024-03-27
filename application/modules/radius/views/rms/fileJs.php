<script async defer
    src="https://maps.google.com/maps/api/js?key=AIzaSyDk_JhHOVjSy5xU4FnKUXcomihclcuU170&q=Space+Needle,Seattle+WA&sensor=false&libraries=places&v=weekly">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    let arrayUserIdExcept=[];
    let arrayUserIdExceptWeb=[];
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
                                    
                                    <div class="caption">Detail RMS </div>
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
                                                        <a class="label label-primary " data-toggle="tab" href="#tab1_${d.id}">User Web dan Mobile dikecualikan</a>
                                                </li>
                                                <li class="nav-item ">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab4_${d.id}">User Web dan Mobile dibatasi</a>
                                                </li>
                                                <li class="nav-item ">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab2_${d.id}">User IFCS dikecualikan</a>
                                                </li>
                                                <li class="nav-item ">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab5_${d.id}">User IFCS dibatasi</a>
                                                </li>                                                
                                                <li class="nav-item ">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab3_${d.id}">Golongan Kendaraan</a>
                                                </li>

                                            </ul>
                        
                                            <div class="tab-content " >
                                                
                                                <div class="tab-pane active " id="tab1_${d.id}" role="tabpanel" >
                                                
                                                <div class="row ${d.hiddenWeb}">
                                                    <div class="col-md-12" ></div>

                                                        <div class="col-md-6" >
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables_${d.rms_code}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    User Web dan Mobile
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th> 
                                                                        <th>
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            EMAIL USER
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </th>
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

                                             
                                                <div class="tab-pane  ${d.hiddenIfcs}" id="tab2_${d.id}" role="tabpanel" >
                                                    
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
                                                                                    User IFCS
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th> 
                                                                        <th>
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            EMAIL USER
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </th>
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
                                                
                                               
                                                <div class="tab-pane  ${d.hiddenIfcs}" id="tab5_${d.id}" role="tabpanel" >
                                                
                                                    <div class="row">
                                                        <div class="col-md-12" ></div>
                                                            <div class="col-md-6" >
                                                                <p></p>
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTablesIfcsLimit_${d.rms_code}" style=" width: 250px;">
                                                                <thead>
                                                                        <tr>
                                                                            <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                                <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                    <div class="input-group select2-bootstrap-prepend">
                                                                                        User IFCS
                                                                                    </div>

                                                                                    <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                                <div>
                                                                            
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>NO</th> 
                                                                            <th>
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                EMAIL USER
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            </th>
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
                                                    <div class="col-md-12"  style="color:red; font-style: italic; font-size:11px" align="left">
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
                                                                                    Golongan Kendaraan
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                            
                                                                        <th>NO</th> 
                                                                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                                            GOLONGAN KENDARAAN
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </th>
                                                                    </tr>
                                                                </thead>                                    
                                                            </table>      
                                                        </div>
                                                    </div>

                                                </div>  
                                                
                                                
                                                <div class="tab-pane   ${d.hiddenWeb}" id="tab4_${d.id}" role="tabpanel" >
                                                

                                                    <div class="row">
                                                        <div class="col-md-12" ></div>

                                                            <div class="col-md-6" >
                                                                <p></p>
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTablesWeblimit_${d.rms_code}" style=" width: 250px;">
                                                                    <thead>
                                                                        <tr>
                                                                            <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                                <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                    <div class="input-group select2-bootstrap-prepend">
                                                                                        User Web dan Mobile
                                                                                    </div>

                                                                                    <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                                <div>
                                                                            
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>NO</th> 
                                                                            <th>
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                EMAIL USER
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            </th>
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
                console.log(element)
                if(user == "web")
                {                    
                    btn = `<a class="btn btn-danger " onclick=deleteUserWeb('${element}') title="hapus" ><i class="fa fa-trash-o"></i></a>`
                    dataExeption += `<input type="hidden" id="${element}" value="${emailValue}" name="webExp[${totalData}]">`
                }
                else
                {
                    btn = `<a class="btn btn-danger " onclick=deleteUserIfcs('${element}') title="hapus"><i class="fa fa-trash-o"></i></a>`
                    dataExeption += `<input type="hidden" id="${element}" value="${emailValue}" name="ifcsExp[${totalData}]">`
                }
                // ini untuk set data di datatable
                email[totalData] = ["", emailValue,btn];         
                                
            });

            $(`#${idExeption}`).html(dataExeption);
            return email;
        }        
        detailUserWebExp(data)
        {
            const getTable = $(`#detailDataTables_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/detailUserWebExp') ?>",
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
                        {"data": "account_id", "orderable": false, "className": "text-left"},
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

        detailUserWebLimit(data)
        {
            const getTable = $(`#detailDataTablesWeblimit_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/detailUserWebLimit') ?>",
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
                        {"data": "email", "orderable": false, "className": "text-left"},
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
                    var $searchInput = $(`div#detailDataTablesWeblimit_${data.rms_code}_filter input`);
                    var data_tables = $(`#detailDataTablesWeblimit_${data.rms_code}`).DataTable();
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

        detailUserIfcsExp(data)
        {
            const getTable = $(`#detailDataTables2_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/detailUserIfcsExp') ?>",
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
                        {"data": "account_id", "orderable": false, "className": "text-left"},
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
        
        detailUserIfcsLimit(data)
        {
            const getTable = $(`#detailDataTablesIfcsLimit_${data.rms_code}`).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/detailUserIfcsLimit') ?>",
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
                        {"data": "email", "orderable": false, "className": "text-left"},
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
                    var $searchInput = $(`div#detailDataTablesIfcsLimit_${data.rms_code}_filter input`);
                    var data_tables = $(`#detailDataTablesIfcsLimit_${data.rms_code}`).DataTable();
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
                        {"data": "vehicle_class_name", "orderable": false, "className": "text-left"},
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
                            $(`#detailDataTables${tab}_${json.data['rms_code']}`).DataTable().ajax.reload(null, false);
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
        
        showModal(lat, long, rmsCode)
        {
            $('#modalView').modal('show');
            $('#rmsCode').val(rmsCode);
            // console.log(lat)
            // console.log(long)
            const radius = $(`#view_${rmsCode}`).attr("data-radius");
            const radiusType =  $(`#view_${rmsCode}`).attr("data-radiusType");
            const html = `
                            KODE RMS : ${rmsCode} <br>
                            RADIUS : ${radius} ${radiusType}<br>
                            LONG : ${long}<br>
                            LAT : ${lat} <br> `;
            $("#infoView").html(html);

            const dataRadius = {radius: radius, radiusType: radiusType};
            initMapView(lat, long, dataRadius);
        }

        //Exceptions IFCS
        // toExceptNull(){
        //     $(`#inputExceptUser_${id}`).remove();
        //     this.reloadTableUserLimited(); 
        //     this.reloadTableUserExcept(); 
            
        // }

        reloadTableUserLimited() 
        {
            $('#tableUserLimited').DataTable().ajax.reload();            
                        
        }

        tableUserExcept() 
        {

            $('#tableUserLimitedExcept').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/getUserExceptIfcs') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idMemberExcept = $("input[name='idMemberExcept[]']").map(function(){return $(this).val();}).get();
                        d.idMemberExcept = arrayUserIdExcept
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

        tableUserLimited(data) 
        {

            $('#tableUserLimited').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/getUserIfcs') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idMemberExcept = $("input[name='idMemberExcept[]']").map(function(){return $(this).val();}).get();
                        d.idMemberExcept = arrayUserIdExcept
                        d.idData = $("#idData").val();
                        d.idNumber= data
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

        reloadTableUserExcept() 
        {
            $('#tableUserLimitedExcept').DataTable().ajax.reload();            
                        
        }        

        toException(id,email)
        {
            let idData= $("#idData").val();
            if(idData==1)
            {

                let inputExceptUserDiv = `<input type="hidden" id="inputExceptUser_${id}" type='hidden' name='idMemberExcept[${id}]' value='${id}' >`
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
    
            }
            else
            {
                let inputExceptUserDiv = `<input type="hidden" id="inputExceptUser_${id}" type='hidden' name='idMemberExcept[${id}]' value='${id}' >`
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

        //Exceptions Web & Mobile
        
        reloadTableUserLimitedWeb() 
        {
            $('#tableUserLimitedWeb').DataTable().ajax.reload();            
                        
        }
        tableUserLimitedWeb() 
        {

            $('#tableUserLimitedWeb').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/getUser') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idMemberExcept = $("input[name='idMemberExcept[]']").map(function(){return $(this).val();}).get();
                        d.idMemberExcept = arrayUserIdExceptWeb
                        d.idDataWeb = $("#idDataWeb").val();
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
                    var $searchInput = $('div #tableUserLimitedWeb_filter input');
                    var data_tables = $('#tableUserLimitedWeb').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                }
                            
            });

                                    
        }

        tableUserExceptWeb() 
        {

            $('#tableUserLimitedExceptWeb').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('radius/rms/getUserExcept') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.idMemberExcept = $("input[name='idMemberExcept[]']").map(function(){return $(this).val();}).get();
                        d.idMemberExcept = arrayUserIdExceptWeb
                        d.idDataWeb = $("#idDataWeb").val()
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
                    var $searchInput = $('div #tableUserLimitedExceptWeb_filter input');
                    var data_tables = $('#tableUserLimitedExceptWeb').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });

                                    
        }

        reloadTableUserExceptWeb() 
        {
            $('#tableUserLimitedExceptWeb').DataTable().ajax.reload();            
                        
        }        

        toExceptionWeb(id,email)
        {
            let idDataWeb= $("#idDataWeb").val();
            if(idDataWeb==1)
            {

                let inputExceptUserDivWeb = `<input type="hidden" id="inputExceptUserWeb_${id}" type='hidden' name='idMemberExceptWeb[${id}]' value='${id}' >`
                $("#inputExceptUserDivWeb").append(inputExceptUserDivWeb);
                
                let table2 = $("#tableUserLimitedExceptWeb").DataTable();  
    
                const btn =`<div class='btn btn-danger transferDataLimit' title='Pindah Ke Pembatasan'  onClick=myData.toLimitWeb('${id}') >
                                    <i class='fa fa-arrow-left' aria-hidden='true'></i>
                                </div>`  
    
                arrayUserIdExceptWeb.push(id)      
            }            
            else
            {
                const filterDataWeb =arrayUserIdExceptWeb.filter(
                    arrayUserIdExceptWeb => arrayUserIdExceptWeb != id
                );
    
                arrayUserIdExceptWeb=[];
                arrayUserIdExceptWeb=filterDataWeb;
    
                $(`#inputExceptUserWeb_${id}`).remove();
            }
            
            /*
            let dataArrr=[btn, email]
            table2.row.add(dataArrr).draw().node();
            */

            this.reloadTableUserLimitedWeb(); 
            this.reloadTableUserExceptWeb(); 

        }



        toLimitWeb(id)
        {
            let idDataWeb= $("#idDataWeb").val();
            if(idDataWeb==1)
            {

                const filterDataWeb =arrayUserIdExceptWeb.filter(
                    arrayUserIdExceptWeb => arrayUserIdExceptWeb != id
                );
    
                arrayUserIdExceptWeb=[];
                arrayUserIdExceptWeb=filterDataWeb;
    
                $(`#inputExceptUserWeb_${id}`).remove();
    
            }
            else
            {
                let inputExceptUserDivWeb = `<input type="hidden" id="inputExceptUserWeb_${id}" type='hidden' name='idMemberExceptWeb[${id}]' value='${id}' >`
                $("#inputExceptUserDivWeb").append(inputExceptUserDivWeb);
                
                let table2 = $("#tableUserLimitedExceptWeb").DataTable();  

                const btn =`<div class='btn btn-danger transferDataLimit' title='Pindah Ke Pembatasan'  onClick=myData.toLimitWeb('${id}') >
                                    <i class='fa fa-arrow-left' aria-hidden='true'></i>
                                </div>`  

                arrayUserIdExceptWeb.push(id)         
            }

                this.reloadTableUserLimitedWeb(); 
                this.reloadTableUserExceptWeb(); 

        }
        
        confirmationAction2(message, url, rmsCode) {
            alertify.confirm(message, function (e) {
                if (e) {
                    myData.returnConfirmation(url, rmsCode)
                }
            });
        }        
        
        returnConfirmation(url, rmsCode)
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
                        $(`#detailDataTables_${rmsCode}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTables2_${rmsCode}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTables3_${rmsCode}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTablesWeblimit_${rmsCode}`).DataTable().ajax.reload(null, false);
                        $(`#detailDataTablesIfcsLimit_${rmsCode}`).DataTable().ajax.reload(null, false);
                        
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
       
    }

    function initMapView(getLat, getLong, radius) {

        var midPoint = {lat: parseFloat(getLat), lng: parseFloat(getLong)};
        // console.log(midPoint)
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,                      
            center: midPoint,
            disableDefaultUI: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP

        });

        var radiusCount = 1000;
        if(radius.radiusType.toUpperCase() == "M")
        {
            radiusCount = 1;
        }
        var getRadius =parseInt(radius.radius);

        if(getLat && getLong ){
            var midPoint = {lat: parseFloat(getLat), lng: parseFloat(getLong)};
            var totRadius = getRadius * radiusCount;
            
            var antennasCircle = new google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                // strokeWeight: 2,
                strokeWeight: 0,
                fillColor: "#FF0000",
                fillOpacity: 0.35,
                map: map,
                clickable: false,
                // center: midPoint,
                center: new google.maps.LatLng(getLat, getLong),
                radius: totRadius

            });
        map.fitBounds(antennasCircle.getBounds());
        addMarker(midPoint);
        }

    }
    
    $("#print").click(function() {  

        var target = document.getElementById("map");
        var rmsCode = $('#rmsCode').val();

        html2canvas(target, {
            useCORS: true
        })
        .then(function (canvas) {
            var canvasImg = canvas.toDataURL("image/png");
            // console.log(canvasImg);
            // $('#img-out').html('<img src="' + canvasImg + '" alt="">');
            // document.getElementById("id_map_base64").value = canvas.toDataURL('image/png');
            downloadBase64File(canvasImg, "MAPS_" + rmsCode + ".png");
        })
        .catch(function (err) {
            // console.log(err);
        });
    
    });

    // Add marker 
    function addMarker(location) {
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }

    function downloadBase64File(base64Data, fileName) {
        //const linkSource = `data:${contentType};base64,${base64Data}`;
        const linkSource = base64Data;
        // console.log(linkSource)
        const downloadLink = document.createElement("a");
        downloadLink.href = linkSource;
        downloadLink.download = fileName;
        downloadLink.click();
    }


</script>