<script>
    class MyData{

        loadData() 
        {
            var table= $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('vaccine_parameter/vaccineParam') ?>",
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
                        {"data": "assessment_type", "orderable": true, "className": "text-left"},
                        {"data": "assessment_type_test", "orderable": true, "className": "text-left"},
                        {"data": "start_date", "orderable": true, "className": "text-left"},
                        {"data": "end_date", "orderable": true, "className": "text-left"},
                        // {"data": "min_age", "orderable": true, "className": "text-left"},
                        {"data": "minUsiaDetail", "orderable": true, "className": "text-left"},
                        {"data": "under_age_reason", "orderable": true, "className": "text-left"},
                        {"data": "pedestrian", "orderable": true, "className": "text-center"},
                        {"data": "vehicle", "orderable": true, "className": "text-center"},   
                        {"data": "web", "orderable": true, "className": "text-center"},
                        {"data": "mobile", "orderable": true, "className": "text-center"},
                        {"data": "ifcs", "orderable": true, "className": "text-center"},
                        {"data": "b2b", "orderable": true, "className": "text-center"},
                        {"data": "pos_vehicle", "orderable": true, "className": "text-center"},
                        {"data": "pos_passanger", "orderable": true, "className": "text-center"},
                        {"data": "mpos", "orderable": true, "className": "text-center"},
                        {"data": "vm", "orderable": true, "className": "text-center"},
                        {"data": "verifikator", "orderable": true, "className": "text-center"},
                        {"data": "web_cs", "orderable": true, "className": "text-center"},
                        // {"data": "vaccine_active", "orderable": true, "className": "text-center"},
                        {"data": "test_covid_active", "orderable": true, "className": "text-center"},
                        // {"data": "timers", "orderable": true, "className": "text-center"},
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
                    myData.detailPort(row.data().id)
                    myData.detailVehicle(row.data().id)                
                }                
                
            } );  
                                    
        }
        formatDate = (date) => {
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
        detailPort(vaccineParamId) 
        {
            var table= $('#portDataTables_'+vaccineParamId).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('vaccine_parameter/vaccineParam/detailPort') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.vaccineParamId = vaccineParamId;
                        // d.team = document.getElementById('team').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "autoWidth":false,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "port_name", "orderable": true, "className": "text-left","width": "15px"},                    
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
                "order": [[ 0, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div #portDataTables_'+vaccineParamId+'_filter input');
                    var data_tables = $('#portDataTables_'+vaccineParamId).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });
                                    
        }

        detailVehicle(vaccineParamId) 
        {
            var table= $('#vehicleDataTables_'+vaccineParamId).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('vaccine_parameter/vaccineParam/detailVehicle') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.vaccineParamId = vaccineParamId;
                        // d.team = document.getElementById('team').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "autoWidth":false,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "vehicle_class_name", "orderable": true, "className": "text-left","width": 5},                    
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
                    var $searchInput = $('div #vehicleDataTables_'+vaccineParamId+'_filter input');
                    var data_tables = $('#vehicleDataTables_'+vaccineParamId).DataTable();
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
        
        format_16082021 ( d ) 
        {
            // `d` is the original data object for the row
            return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                '<tr>'+
                    '<td>Full name:</td>'+
                    '<td>'+d.assessment_type+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Extension number:</td>'+
                    '<td>'+d.assessment_type+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Extra info:</td>'+
                    '<td>And any further details here (images etc)...</td>'+
                '</tr>'+
            '</table>';
        }             
        
        format ( d ) 
        {
            // console.log(d.add_detail_vehicle)
            var html = `<div style="background-color:#e1f0ff; padding:10px;">
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">Detail Parameter Tipe Assesment </div>
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
                                                        <a class="label label-primary " data-toggle="tab" href="#tab1_${d.id}">Detail Pelabuhan</a>
                                                </li>
                                                <li class="nav-item">
                                                        <a class="label label-primary " data-toggle="tab" href="#tab2_${d.id}">Detail Kelas Kendaraan</a>
                                                </li>            
                                            </ul>
                        
                                            <div class="tab-content " >

                                                <div class="tab-pane active" id="tab1_${d.id}" role="tabpanel" >
                                                    <div class="row">
                                                        <div class="col-md-8" style="width:40%">
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="portDataTables_${d.id}">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=4 style="text-align:left; padding-left:14px;  " >
                                                                        
                                                                            <div class="col-sm-12 form-inline" style="margin-top:20px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Detail Pelabuhan
                                                                                </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th> 
                                                                        <th>NAMA PELABUHAN</th>
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

                                                <div class="tab-pane" id="tab2_${d.id}" role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-md-8" style="width:40%">
                                                            <p></p>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="vehicleDataTables_${d.id}" style=" width: 250px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan=4 style="text-align:left; padding-left:14px;  "  >
                                                                            <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                <div class="input-group select2-bootstrap-prepend">
                                                                                    Detail Golongan Kendaraan
                                                                                </div>

                                                                                <div class="input-group select2-bootstrap-prepend pull-right">
                                                                                    ${d.add_detail_vehicle}
                                                                                </div>
                                                                            <div>
                                                                        
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>NO</th> 
                                                                        <th>GOLONGAN KENDARAAN</th>
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

        getVaccineAndTest()
        {
           
            let optionMaxVaccine =`  <option value="">Pilih</option><option value=0>tidak vaksin</option>`
            for (let index = 0; index < getMaxVaccine; index++) {
                optionMaxVaccine += ` <option value=${index + 1 }>vaksin ${index + 1 }</option> `;
            }

            let optionTestCovid =`  <option value="">Pilih</option>`
            getTestCovid.forEach(element => {
                let testType = element.test_type=='empty'?'tidak perlu tes':element.test_type;
                optionTestCovid += ` 
                                    <option value=${element.order_value}>${testType}</option>
                                `;
            });

            let returnData = {optionMaxVaccine : optionMaxVaccine, optionTestCovid : optionTestCovid}

            return returnData;

        }

        addMinAge(id)
        {   
            const option = this.getVaccineAndTest()

            let html = `
                <div class="row rowMinAge" style="margin:10px 0px 0px 0px; padding-top:10px; background-color:#f6f6f6; border-radius: 25px; display:none" id="rowMinAge_${id}" >
                    <div class="col-sm-4 form-group">
                        <label>Min Usia<span class="wajib">*</span></label> 
                        <input type="number" name="minAge[${id}]" id="minAge${id}" class="form-control" required placeholder="Minimal Usia" min=1  required>
                        <input type="hidden" name="idMinAge[${id}]" value="${id}"  required>
                    </div>

                    <div class="col-sm-8 form-group">
                        <div  onClick=myData.deleteMinAge(${id}) class="btn btn-sm btn-danger pull-right" id="deleteMaxAge_${id}" title="Hapus Min Usia" ><i class="fa fa-trash"></i></div>
                    </div>

                    <div class="col-sm-12 form-group"></div>

                    <div class="col-sm-12 form-group">

                        <div class="col-sm-12 form-group">
                            <div  class="btn btn-sm btn-warning add-vaccine-test"  data-idMinAge="${id}"  title="Tambah Vaksin Status" ><i class="fa fa-plus"></i> Vaksin</div>
                        </div>

                        <div class="col-sm-6 form-group classVaccineTest_${id} classVaccineTest_${id}_0 ">
                            <label>Vaksin ke <span class="wajib">*</span></label> 
                            <select name="vaccineCovid_${id}[0]" id="vaccineCovid_${id}_0" class="form-control  vaccineCovid_${id}" data-id=0 required >
                                ${option.optionMaxVaccine}
                            </select>
                        </div>

                        <div class="col-sm-5 form-group classVaccineTest_${id}_0">
                            <label>Tes Covid <span class="wajib">*</span></label>
                            <select name="testCovid_${id}[0]" id="testCovid_${id}_0" class="form-control  testCovid" required>
                                ${option.optionTestCovid}
                            </select>
                        </div>

                        <div class="col-sm-1 form-group classVaccineTest_${id}_0">
                
                            <div style="padding:10px;"></div>
                            <div  onclick=myData.deleteVaccineTest(${id},0) style="border-radius:5px" class="btn btn-md btn-danger pull-left" id="deleteVaccineTest_${id}_0" title="Hapus Vaksin Status" ><i class="fa fa-trash"></i></div>
                                                
                        </div>

                        <div class="col-sm-12 form-group" id="vaccineTestContent_${id}" ></div>
                    </div>
                </div>
            `


            $( html ).insertBefore( $( "#contentMinAge" ) );
            $(`#rowMinAge_${id}`).slideDown("slow", function(){
                $(this).show()
            })
            
            $(`#vaccineCovid_${id}_0, #testCovid_${id}_0`).select2();
            
            $(".add-vaccine-test").off().on("click",function(){
                // data-idMinAge="0" data-idVaccineTest="0"                
                
                let idMinAge= $(this).attr("data-idMinAge");
                let dataLength = $(`.vaccineCovid_${idMinAge}`).length
                let data = $(`.vaccineCovid_${idMinAge}`).map(function(){ 
                    return $(this).attr(`data-id`) 
                }).toArray();

                let idVaccineTest= data[dataLength-1] // ambil data class yang terakhir


                myData.addVaccineTest(idMinAge, idVaccineTest)
            })

            return id++;
        }

        addVaccineTest(id,idVaccineTest)
        {
            const option = this.getVaccineAndTest()
            let idTest = parseInt(idVaccineTest)
            idTest += 1;        

            let html =`

            <div class="col-sm-6 form-group classVaccineTest_${id} classVaccineTest_${id}_${idTest} " style="display:none">
                <label>Vaksin ke <span class="wajib">*</span></label> 
                <select name="vaccineCovid_${id}[${idTest}]" id="vaccineCovid_${id}_${idTest}" class="form-control  vaccineCovid_${id}" data-id=${idTest} required >
                    ${option.optionMaxVaccine}
                </select>
            </div>

            <div class="col-sm-5 form-group classVaccineTest_${id}_${idTest}" style="display:none" >

                    <label>Tes Covid <span class="wajib">*</span></label> 
                    <select name="testCovid_${id}[${idTest}]" id="testCovid_${id}_${idTest}" class="form-control  testCovid"  required>
                        ${option.optionTestCovid}
                    </select>

            </div>

            <div class="col-sm-1 form-group classVaccineTest_${id}_${idTest} " style="display:none">
                
                    <div style="padding:10px;"></div>
                    <div  onclick=myData.deleteVaccineTest(${id},${idTest}) style="border-radius:5px" class="btn btn-md btn-danger pull-left" id="deleteVaccineTest_${id}_${idTest}" title="Hapus Vaksin Status" ><i class="fa fa-trash"></i></div>
                                        
            </div>`

            // getMaxVaccine  di define  di index.php
            // console.log(getMaxVaccine+"-"+idTest)
            
            if(getMaxVaccine >= $(`.vaccineCovid_${id}`).length ) // jika vaksin masih kurang dari  maximal vaksin maka di bolehkan add
            {            
                $( html ).insertBefore( $( `#vaccineTestContent_${id}` ) );
                $(`.classVaccineTest_${id}_${idTest}`).slideDown("slow", function(){
                    $(this).show()
                })
            }
            else
            {
                toastr.error(' Input Vaksin sudah mencapai maksimal ', 'Gagal Tambah');
            }

            $(`#vaccineCovid_${id}_${idTest}:not(.normal), #testCovid_${id}_${idTest}:not(.normal)`).select2()


        }
        deleteVaccineTest(id,idVaccineTest)
        {
                        
            let dataLength =  $(`.vaccineCovid_${id}`).length;
            if(dataLength>1)
            {
                $(`.classVaccineTest_${id}_${idVaccineTest}`).slideUp("slow", function(){
                    $(this).remove()
                })

            }
            else
            {
                toastr.error('Setidaknya harus ada satu Vaksin dan Tes', 'Gagal Hapus');
            }
        }
        deleteMinAge(id)
        {
            let dataLength =  $(`.rowMinAge`).length;

            if(dataLength>1)
            {
                $($(`#rowMinAge_${id}`)).slideUp("slow", function(){

                    $(this).remove()
                })
            }
            else
            {
                toastr.error('Setidaknya harus ada satu Min usia ', 'Gagal Hapus');
            }
        }

    }
</script>