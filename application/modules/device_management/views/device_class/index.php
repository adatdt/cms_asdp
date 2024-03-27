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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days"))?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding" style="padding-left: 10px"><?php echo $btn_add; ?></div>
                    <!-- <div class="pull-right btn-add-padding"><?php echo $btn_excel; ?></div> -->
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="port" class="form-control select2 input-small" dir="port" name="port">
                                        <option value="">Pilih</option>
                                        <?php foreach($port as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div> 

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Servis</div>
                                    <select id="service" class="form-control select2 input-small" dir="tes" name="service">
                                        <option value="">Pilih</option>
                                        <?php foreach($service as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div> 

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tipe Perangkat</div>
                                    <select id="device_type" class="form-control select2 input-small" dir="tes" name="device_type">
                                        <option value="">Pilih</option>
                                        <?php foreach($device_type as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->terminal_type_id); ?>"><?php echo strtoupper($value->terminal_type_name); ?></option>
                                        <?php } ?>
                                    </select>
                                </div> 

                                <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nama Perangkat
                                            <i class="fa fa-angle-down"></i>
                                        </button>    
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="changeSearch('Nama Perangkat','deviceName')">Nama Perangkat</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="changeSearch('Kode Perangkat','terminalCode')">Kode Perangkat</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="changeSearch('S/N','serialNumber')">S/N</a>
                                            </li>                                            

                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control input-small" placeholder="Cari Data" data-name="deviceName" name="searchData" id="searchData"> 
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

                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>KODE PERANGKAT</th>
                                <th>NAMA PERANGKAT</th>
                                <th>ID TIPE TERMINAL</th>
                                <th>NAMA TIPE PERANGKAT</th>
                                <th>SERVIS</th>
                                <th>NAMA CHANNEL</th>
                                <th>PELABUHAN</th>
                                <th>DERMAGA</th>
                                <th>S/N</th>
                                <th>PAIRING POS</th>
                                <th>TIPE KAPAL</th>
                                <th>LINTAS KELAS</th>
                                <th>USER PHONE</th>
                                <th>EXTENTION PHONE</th>
                                <th>CCTV PATH</th>
                                <th>GOLONGAN KENDARAAN</th>
                                <th>IP PERANGKAT</th>
                                <th>LEBIH BAYAR/ KURANG BAYAR</th>
                                <th>SENSOR</th>                                
                                <th>STATUS</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AKSI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            </tr>
                        </thead>
                        <tfoot></tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
    data: csfrData
    });


    function showText(id)
    {
        var x = document.getElementById(`text_${id}`);
        var textBtn = document.getElementById(`btndetail_${id}`);
        
        if (x.style.display === "none") {
            x.style.display = "block";
            textBtn.innerHTML="Sembunyikan";
            
        } else {
            x.style.display = "none";
            textBtn.innerHTML="...";
        }
    }
$("#btndownload").click(function(event){

    window.location.href="<?php echo site_url('configuration/users/download') ?>";
});

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('device_management/device_class') ?>",
                "type": "POST",
                "data": function(d) {
                    d.service = document.getElementById('service').value;
                    d.port = document.getElementById('port').value;
                    d.device_type = document.getElementById('device_type').value;
                    d.searchData=document.getElementById('searchData').value;
                    d.searchName=$("#searchData").attr('data-name');                    
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
            "filter": false,
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "terminal_code", "orderable": true, "className": "text-left"},
                    {"data": "terminal_name", "orderable": true, "className": "text-left"},
                    {"data": "terminal_type_id", "orderable": true, "className": "text-left"},
                    {"data": "terminal_type_name", "orderable": true, "className": "text-left"},
                    {"data": "service_name", "orderable": true, "className": "text-left"},
                    {"data": "channel", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "dock_name", "orderable": true, "className": "text-left"},
                    {"data": "imei", "orderable": true, "className": "text-left"},
                    {"data": "pairing_pos_name", "orderable": true, "className": "text-left"},
                    {"data": "class_ship_name", "orderable": true, "className": "text-left"},
                    {"data": "cross_class", "orderable": true, "className": "text-center"},
                    {"data": "username_phone", "orderable": true, "className": "text-center"},
                    {"data": "extension_phone", "orderable": true, "className": "text-center"},
                    {"data": "cctv_path", "orderable": true, "className": "text-left"},
                    {"data": "vehicle_class_id", "orderable": true, "className": "text-left"},
                    {"data": "ip_device", "orderable": true, "className": "text-center"},
                    {"data": "enable_overpaid_underpaid", "orderable": true, "className": "text-center"},
                    {"data": "enable_sensor", "orderable": true, "className": "text-center"},
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

changeSearch=(x,name)=>
{
    $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
    $("#searchData").attr('data-name', name);

}	
    

$(document).ready(function () {
    table.init();
    

    $("#service").on("change",function(){
        table.reload();
    });

    $("#port").on("change",function(){
        table.reload();
    });

    $("#device_type").on("change",function(){
        table.reload();
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
