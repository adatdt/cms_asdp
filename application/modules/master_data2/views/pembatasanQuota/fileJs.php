<script type="text/javascript" >
    class MyData{
        loadData() {
            const table= $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanQuota') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = $("#dateFrom").val();
                        d.dateTo = $("#dateTo").val();
                        d.shipClass = $("#shipClass").val();
                        d.portId = $("#portId").val();
                        d.vehicleClassId = $("#vehicleClassId").val();
                    },
                },

                "serverSide": true,
                "processing": true,                
                "searching": false,
                "columns": [
/*                    {
                            "className":      'details-control',
                            "orderable":      false,
                            "data":           null,
                            "defaultContent": '<span  class="label label-success"><i class="fa fa-plus" aria-hidden="true"></i></span>',
                            "targets": 0
                        },                    */
                        {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "jenis_pj", "orderable": true, "className": "text-left"},
                        {"data": "golongan", "orderable": true, "className": "text-left"},
                        {"data": "quota", "orderable": true, "className": "text-right"},
                        {"data": "total_lm", "orderable": true, "className": "text-right"},
                        {"data": "start_date", "orderable": true, "className": "text-left"},
                        {"data": "end_date", "orderable": true, "className": "text-left"},
                        {"data": "depart_time", "orderable": true, "className": "text-left"},
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
                    // row.child( myData.format(row.data()) ).show();

                    // console.log(row.data());
                    row.child( myData.detailLayout(row.data().id)).show();
                    tr.addClass('shown');  
                    td.html('<span  class="label label-danger"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                    
                    myData.detailData(row.data().id)

                }                
                
            } ); 
        }

        reload() {
            $('#dataTables').DataTable().ajax.reload();
        }

        init() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
        detailLayout(id){
            var html = `<div style="background-color:#e1f0ff; padding:10px;">
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">Detail Pembatasan Member </div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">
                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class=" form-inline " align="left">
                                                <div class="input-group pad-top">
                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData_${id}' >email<i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:;" onclick="myData.changeSearch('email','email')">email</a>
                                                            </li>                                                                                                 
                                                        </ul>
                                                    </div>
                                                    <!-- /btn-group -->
                                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="email" name="searchData_${id}" id="searchData_${id}"> 
                                                </div>   
                                                <div class="input-group pad-top">
                                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" onClick="myData.cariDataDetail()" id="cari_" >
                                                        <span class="ladda-label">Cari</span>
                                                        <span class="ladda-spinner"></span>
                                                    </button>
                                                </div>  

                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="kt-portlet__body">
                                            <div class="row">

                                                <div class="col-md-12" >
                                                    <p></p>
                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="detailDataTables_${id}" style=" width: 250px;">
                                                        <thead>
                                                            <tr>
                                                                <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                    <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                        <div class="input-group select2-bootstrap-prepend">
                                                                            Detail Kendaraan Global
                                                                        </div>

                                                                        <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                    <div>
                                                                
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>NO</th> 
                                                                <th>GOLONGAN</th>
                                                                <th>BATAS QUOTA</th>
                                                                <th>BATAS QUOTA DIGUNAKAN</th>
                                                                <th>SISA BATAS QUOTA</th> 
                                                                <th>TANGGAL</th>
                                                                <th>JAM</th>
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
            `
            // `d` is the original data object for the row
            return html;
        }

        detailData(id){
            const table= $('#detailDataTables_'+id).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/pembatasanQuota/pembatasanQuotaDetail') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.port = document.getElementById('port').value;
                        // d.team = document.getElementById('team').value;
                    },
                },

                "serverSide": true,
                "processing": true,
                "columns": [        
                        {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "golongan", "orderable": true, "className": "text-left"},
                        {"data": "quota", "orderable": true, "className": "text-right"},
                        {"data": "total_quota", "orderable": true, "className": "text-right"},
                        {"data": "used_quota", "orderable": true, "className": "text-right"},
                        {"data": "depart_date", "orderable": true, "className": "text-left"},
                        {"data": "depart_time", "orderable": true, "className": "text-left"},
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
                    var $searchInput = $(`div #detailDataTables_${id}_filter input`);
                    var data_tables = $('#detailDataTables_'+id).DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
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
        estimation(){
            var totalQuota=$("#totalQuota").val();
            var quota=$("#quota").val()
            var action=$("#actions").val()

            var a = totalQuota==""?0:parseInt(totalQuota);
            var b = quota==""?0:parseInt(quota);

            if(quota==0)
            {
                var c=totalQuota;
            }
            else if(action==1)
            {
                var c= a+b;
            }
            else if (action==2)
            {
                var c= a-b;
            }
            else
            {
                var c=0;
            }

            document.getElementById("estimation").value=c;


        }
    }    
</script>