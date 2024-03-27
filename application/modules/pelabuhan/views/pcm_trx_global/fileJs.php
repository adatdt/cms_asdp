<script type="text/javascript">
var csfrData = {};
csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
$.ajaxSetup({
    data: csfrData
});
    class MyData{
        loadData() {
            const table = $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('pelabuhan/pcm_trx_global') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.dateTo = document.getElementById('dateTo').value;
                        d.port = document.getElementById('port').value;
                        d.shipClass = document.getElementById('shipClass').value;
                        d.time = document.getElementById('time').value;
                    },
                    "dataSrc": function ( json ) {
                        //Make your callback here.
                        let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                        let getToken = json[getTokenName];
                        csfrData[getTokenName] = getToken;

                        if( json[getTokenName] == undefined )
                        {
                        csfrData[json.csrfName] = json.tokenHash;
                        }
                            
                        $.ajaxSetup({
                            data: csfrData
                        });
                        
                        
                        return json.data;
                    } 
                },

                "serverSide": true,
                "processing": true,
                "searching": false,
                "columns": [
                        {
                            "className":      'details-control',
                            "orderable":      false,                            
                            "data":           "id_quota_restriction",
                            // "defaultContent": '<span  class="label label-success"><i class="fa fa-plus" aria-hidden="true"></i></span>',
                            "targets": 0
                        },                                        
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "port_name", "orderable": true, "className": "text-left"},
                        {"data": "ship_class_name", "orderable": true, "className": "text-left"},
                        {"data": "depart_date", "orderable": true, "className": "text-left"},
                        {"data": "depart_time", "orderable": true, "className": "text-left"},
                        {"data": "quota", "orderable": true, "className": "text-right"},
                        {"data": "total_quota", "orderable": true, "className": "text-right"},
                        {"data": "used_quota", "orderable": true, "className": "text-right"},
                        {"data": "quota_reserved", "orderable": true, "className": "text-right"},
                        {"data": "total_lm", "orderable": false, "className": "text-right"},
                        {"data": "lmTersedia", "orderable": false, "className": "text-right"},
                        {"data": "lmDigunakan", "orderable": false, "className": "text-right"},
                        // {"data": "quota_restrict", "orderable": false, "className": "text-right"},
                        // {"data": "used_quota_restrict", "orderable": false, "className": "text-right"},
                        // {"data": "total_quota_restrict", "orderable": false, "className": "text-right"},
                        // {"data": "vehicle_class_name", "orderable": false, "className": "text-right"},
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
                    td.html('<span  class="label label-success klik-detail "><i class="fa fa-plus" aria-hidden="true"></i></span>');
                }
                else {
                    // Open this row
                    // row.child( myData.format(row.data()) ).show();

                    // console.log(row.data().id_quota_restriction)

                    if(row.data().id_quota_restriction !=="")
                    {
                        row.child( myData.detailLayout(row.data().id)).show();
                        tr.addClass('shown');
                        td.html('<span  class="label label-danger klik-detail"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                    }


                    let dataParam ={
                        depart_date : row.data().tgl_depart_date,
                        depart_time : row.data().depart_time,
                        port_id : row.data().port_id,
                        ship_class : row.data().ship_class,
                    }
                    
                    // console.log(row.data())
                    myData.detailData(row.data().id,dataParam)

                    $('.select2:not(.normal)').each(function () {
                        $(this).select2({
                            dropdownParent: $(this).parent()
                        });
                    });

                    $("#cari_"+row.data().id).on("click",function(){
                        $(this).button('loading');
                        // $(`#detailDataTables_${row.data().id}`).DataTable().ajax.reload( null, false );
                        $(`#detailDataTables_${row.data().id}`).DataTable().ajax.reload();
                        // $("#cari").button('reset');
                        $('#detailDataTables_'+row.data().id).on('draw.dt', function() {
                            $("#cari_"+row.data().id).button('reset');
                        });
                    });

                }
                
            } );
        }

        reload () {
            $('#dataTables').DataTable().ajax.reload();
        }

        init () {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }

        estimation(){

            var totalQuota=$("#totalQuota").val();
            var quota=$("#quota").val()
            var action=$("#action").val()

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
                var c="";
            }

            document.getElementById("estimation").value=c;

        }

        directUrl(url)
        {
            window.location.href=`${url}`;
        }
        postData2(url,data)
        {
            $.ajax({
                url         : url,
                data        : new FormData($('form')[0]),
                type        : 'POST',
                dataType    : 'json',
                contentType :false,
                processData :false,


                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1){
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        $('#dataTables').DataTable().ajax.reload();

                        console.log(json.data);
                        // jika minta list mana aja yang tidak ke update
                        var arr=json.data;
                        if (arr.length > 0)
                        {
                            showModal('pcm_trx_global/listErr');
                        }
                    }
                    else
                    {
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){
                    $('#box').unblock(); 
                }
            });
        }
        detailLayout(id){
            var html = `<div style="background-color:#e1f0ff; padding:10px;">
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">Detail Quota Restriction </div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">
                                    <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class=" form-inline " align="left">
                                                <div class="input-group ">
                                                    <div class="input-group select2-bootstrap-prepend " >
                                                        <div class="input-group-addon">Golongan</div>
                                                        <select id="vehicleClass_${id}" class="form-control select2" name="vehicleClass">
                                                            <?php foreach ($vehicleClass as $key => $value) { ?>
                                                                <option value="<?= $key ?>"><?= $value ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="input-group ">
                                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in"  id="cari_${id}" >
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
                                                                <th>TOTAL QUOTA RESTRICT</th>
                                                                <th>QUOTA RESTRICT YANG DI GUNAKAN</th>
                                                                <th>QUOTA RESTRICT TERSEDIA</th>
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
        detailData(id,data){
            csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
            $.ajaxSetup({
                data: csfrData
            });
            const table= $('#detailDataTables_'+id).DataTable({
                "ajax": {
                    "url": "<?php echo site_url('pelabuhan/pcm_trx_global/pembatasanQuotaDetail') ?>",
                    "type": "POST",
                    "data": function(d) {
                        // d.port = document.getElementById('port').value;
                        // d.team = document.getElementById('team').value;

                        d.shipClass = data.ship_class;
                        d.portId = data.port_id;
                        d.departDate = data.depart_date;
                        d.departTime = data.depart_time;
                        d.idTable = id;
                        d.vehicleClass=$("#vehicleClass_"+id).val();
                    },
                     "dataSrc": function ( json ) {
                        //Make your callback here.
                        let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                        let getToken = json[getTokenName];
                        csfrData[getTokenName] = getToken;

                        if( json[getTokenName] == undefined )
                        {
                        csfrData[json.csrfName] = json.tokenHash;
                        }
                            
                        $.ajaxSetup({
                            data: csfrData
                        });
                        
                        
                        return json.data;
                    } 
                },

                "serverSide": true,
                "processing": true,
                "searching": false,
                "columns": [        
                        {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                        {"data": "quota", "orderable": true, "className": "text-right"},
                        {"data": "used_quota", "orderable": true, "className": "text-right"},
                        {"data": "total_quota", "orderable": true, "className": "text-right"},
                        {"data": "actions", "orderable": false, "className": "text-center"},
                        // {"data": "depart_date", "orderable": true, "className": "text-left"},
                        // {"data": "depart_time", "orderable": true, "className": "text-left"},
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

