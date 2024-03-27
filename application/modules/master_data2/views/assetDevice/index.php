<style>
    .scrolling 
    {
        max-height: 350px;
        overflow-y: auto;
    }

    .scrolling::-webkit-scrollbar {
        width: 10px;
    }

    .scrolling::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey; 
    border-radius: 10px;
    }    

    .scrolling::-webkit-scrollbar-thumb {
    background: #c1c1c1; 
    border-radius: 10px;
    }

    .scrolling::-webkit-scrollbar-thumb:hover {
        background: #c1c1c1;
    }    

</style>

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>


        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">

                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline pad-top">

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal  Berlaku</div>
                                                <input type="text" class="form-control  input-small" id="dateFrom" readonly placeholder="yyyy-mm-dd">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control  input-small" id="dateTo"  readonly placeholder="yyyy-mm-dd">

                                            </div>         
                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                            <div class="input-group-addon">Pelabuhan</div>
                                                <?= form_dropdown("port",$port,"",' class="form-control select2"  id="port" '); ?>
                                            </div>                                                                       

                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Group Perangkat
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>    
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Group Perangkat','groupCode')">Kode Group Perangkat</a>
                                                        </li>                                                    
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Module','module')">Module</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Gambar','name')">Nama Gambar</a>
                                                        </li>           
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Sumber IP','ip')">Sumber IP</a>
                                                        </li>                                                
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="groupCode" name="searchData" id="searchData"> 
                                            </div>                          

                                            <div class="input-group select2-bootstrap-prepend pad-top">

                                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                                    <span class="ladda-label">Cari</span>
                                                    <span class="ladda-spinner"></span>
                                                </button>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                    <p></p>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>PELABUHAN</th>
                                <th>MODULE</th>
                                <th>TANGGAL BERLAKU</th>
                                <th>KODE GRUP PERANGKAT</th>
                                <th>NAMA GAMBAR</th>
                                <th>KETERANGAN</th>
                                <th>TIPE FILE</th>
                                <th>PATH</th>
                                <th>SUMBER IP</th>
                                <th>SINKRONIS</th>
                                <!-- <th>STATUS</th> -->
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
<?php include("fileJs.php");?>
<script type="text/javascript">
    const myData = new MyData()
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });    

    var table= {
        loadData: function() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data2/assetDevice') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.dateTo = document.getElementById('dateTo').value;
                        d.dateFrom = document.getElementById('dateFrom').value;
                        d.port = document.getElementById('port').value;                        
                        d.searchData=document.getElementById('searchData').value;
                        d.searchName=$("#searchData").attr('data-name');
                    },
                },
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "port_name", "orderable": false, "className": "text-left"},
                        {"data": "module", "orderable": false, "className": "text-left"},
                        {"data": "start_date", "orderable": false, "className": "text-left"},
                        {"data": "group_code_assets", "orderable": false, "className": "text-left"},
                        {"data": "name", "orderable": false, "className": "text-left"},
                        {"data": "desc", "orderable": false, "className": "text-left"},
                        {"data": "file_type", "orderable": false, "className": "text-left"},
                        {"data": "path", "orderable": false, "className": "text-left"},
                        {"data": "ip_local", "orderable": false, "className": "text-left"},
                        {"data": "is_sync", "orderable": false, "className": "text-center"},
                        // {"data": "status", "orderable": true, "className": "text-left"},
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
                "filter":false,
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

            $('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
            });
        },

        reload: function() {
            $('#dataTables').DataTable().ajax.reload();
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };

    function postData2(url,data,y){

        form = $('form')[0];
        formData = new FormData(form);

        $.ajax({
            url         : url,
            data        :formData,
            type        : 'POST',
            // enctype: 'multipart/form-data',
            processData: false,  // Important!
            contentType: false,
            cache:false,
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                    // console.log(allRow.json);
                    let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                    let getToken = json[getTokenName];			

                    csfrData[getTokenName] = getToken;
                    if( json[getTokenName] == undefined )
                    {
                        csfrData[json['csrfName']] = json['tokenHash'];
                    }							
                    $.ajaxSetup({
                        data: csfrData
                    });

                if(json.code == 1){
                    // unblockID('#form_edit');
                    closeModal();
                    toastr.success(json.message, 'Sukses');
                    
                    if(y){
                        $('#grid').treegrid('reload');
                        // ambil_data();
                    }
                    else
                    {
                        $('#dataTables').DataTable().ajax.reload( null, false );
                        $('#t_reward').DataTable().ajax.reload( null, false );
                        $('#dataTables2').DataTable().ajax.reload( null, false );
                        // ambil_data();

                    }
                }else{
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
    function hapus(id)
    {
        let countElement = $(`.group-gambar`).length;
        if(countElement>1)
        {
            $(`#div-upload-${id}`).slideUp("400", function(){
                $(this).remove()
            })
        }
        else
        {
            toastr.error("Minimal satu data file", 'Gagal');
        }
    }
    function validateForm2(id, callback) {
        $(id).validate({
        ignore: 'input[type=hidden], .select2-search__field',
        errorClass: 'validation-error-label',
        successClass: 'validation-valid-label',
        rules: rules,
        messages: messages,

        highlight: function (element, errorClass) {
            $(element).addClass('val-error');
        },

        unhighlight: function (element, errorClass) {
            $(element).removeClass('val-error');
        },

        errorPlacement: function (error, element) {
            if (element.parents('div').hasClass('has-feedback')) {
                error.appendTo(element.parent());
            }

            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                error.appendTo(element.parent());
            }
            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('file-upload')) {
                error.appendTo(element.parent());
            }            

            else {
                error.insertAfter(element);
            }
        },

        submitHandler: function (form) {
            if (typeof callback != 'undefined' && typeof callback == 'function') {
                callback(form.action, getFormData($(form)));
            }
        }
        });
    } 
    

    jQuery(document).ready(function () {
        table.init();

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: "+1m",
            startDate: new Date()
        });

        $("#dateTo").on('change',function() {
            let endDate2 = $(this).val();
            let startDate = $(`#dateFrom`).val();
            if(startDate == "")
            {
                $(`#dateFrom`).val(endDate2);
                var someDate = new Date(endDate2);

                someDate.getDate();
                someDate.setMonth(someDate.getMonth()+1);
                someDate.getFullYear();
                let endDate=myData.formatDate(someDate);

                // destroy ini firts setting
                $('#dateTo').datepicker('remove');
                
                // Re-int with new options
                $('#dateTo').datepicker({
                    format: 'yyyy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    autoclose: true,
                    todayHighlight: true,
                    endDate: endDate,
                    startDate: endDate2
                });

                $('#dateTo').val(endDate2).datepicker("update")            

            }            
        })                  

        $("#dateFrom").on('change',function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=myData.formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo').datepicker('remove');
            
              // Re-int with new options
            $('#dateTo').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datepicker("update")            
        });

        $("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });       
        
    });



</script>
