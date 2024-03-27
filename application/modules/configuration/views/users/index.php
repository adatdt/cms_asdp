<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent . '">' . $parent; ?></a>
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
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?>

                        <?php if ($btn_excel) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <?php } ?>                    
                        <!-- <button class="btn  btn-sm btn-warning" id="btndownload" >Download</button> -->
                    </div>
                </div>
                <div class="portlet-body">

                            <div class="table-toolbar">
                                <div class="row">
                                    <div class="col-sm-12 form-inline">

                                        <div class="input-group select2-bootstrap-prepend">
                                            <div class="input-group-addon">User Group</div>
                                            <select class="form-control  input-small select2" id="user_group" >
                                                <option value="">Pilih</option>
                                                <?php  foreach($user_group as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id) ?>">
                                                        <?php echo $value->name ?>
                                                    </option>
                                                <?php } ?>
                                            </select> 
                                        </div>    

                                        <div class="input-group select2-bootstrap-prepend">
                                            <div class="input-group-addon">Pelabuhan</div>
                                            <select class="form-control  input-small select2" id="port" >
                                                <option value="">Pilih</option>
                                                <option value="<?= $this->enc->encode('a') ?>" >SEMUA PELABUHAN</option>
                                                <?php  foreach($port as $keyP=>$valueP) {?>
                                                    <option value="<?php echo $this->enc->encode($valueP->id) ?>">
                                                        <?php echo $valueP->name ?>
                                                    </option>
                                                <?php } ?>
                                            </select> 
                                        </div>

                                        <div class="input-group pad-top">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Username
                                                    <i class="fa fa-angle-down"></i>
                                                </button>    
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="javascript:;" onclick="changeSearch('Username','username')">Username</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" onclick="changeSearch('Nama Depan','firstName')">Nama Depan</a>
                                                    </li>                                                        
                                                    <li>
                                                        <a href="javascript:;" onclick="changeSearch('Nama Belakang','lastName')">Nama Belakang</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" onclick="changeSearch('Username Phone	','usernamePhone')">Username Phone</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" onclick="changeSearch('Extension Phone	','extentionPhone')">Extension Phone</a>
                                                    </li>                                                                                                        
                                                </ul>
                                            </div>
                                            <!-- /btn-group -->
                                            <input type="text" class="form-control" placeholder="Cari Data" data-name="username" name="searchData" id="searchData"> 
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
                                <th class="text-center">NO</th>
                                <th>NAMA DEPAN</th>
                                <th>NAMA BELAKANG</th>
                                <th>USERNAME</th>
                                <th>GROUP</th>
                                <th>PELABUHAN</th>
                                <th>USERNAME PHONE</th>
                                <th>EXTENSION PHONE</th>
                                <th>LOGIN CMS</th>
                                <th>LOGIN VALIDATOR</th>
                                <th>LOGIN E-KTP READER</th>
                                <th>LOGIN CS</th>
                                <th>LOGIN POS </th>
                                <th>VERIFIKATOR </th>
                                <th>COMMAND CENTER </th>
                                <th>STATUS</th>
                                <th class="text-center">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                AKSI
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </th>
                            </tr>
                        </thead>
                        <tfood></tfood>
                    </table>              
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.css'); ?>">
<script src="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.js'); ?>"></script>
<script type="text/javascript">
var csfrData = {};
csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
$.ajaxSetup({
data: csfrData
});
$("#btndownload").click(function(event){

    window.location.href="<?php echo site_url('configuration/users/download') ?>";
});

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('configuration/users/ajaxlist_user') ?>",
                "type": "POST",
                "data": function(d) {
                     d.user_group =document.getElementById('user_group').value;
                     d.port =document.getElementById('port').value;
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
                    {"data": "number", "orderable": false, "className": "text-center", "width": 20},
                    {"data": "first_name", "orderable": false},
                    {"data": "last_name", "orderable": false},
                    {"data": "username", "orderable": false},
                    {"data": "group_name", "orderable": false},
                    {"data": "port_name", "orderable": false},
                    {"data": "username_phone", "orderable": false},
                    {"data": "extension_phone", "orderable": false},
                    {"data": "admin_pannel_login", "orderable": false ,"className": "text-center"},
                    {"data": "validator_login", "orderable": false ,"className": "text-center"},
                    {"data": "e_ktp_reader_login", "orderable": false ,"className": "text-center"},
                    {"data": "cs_login", "orderable": false,"className": "text-center"},
                    {"data": "pos_login", "orderable": false,"className": "text-center"},
                    {"data": "verifier_login", "orderable": false,"className": "text-center"},
                    {"data": "command_center_login", "orderable": false,"className": "text-center"},
                    {"data": "status", "orderable": false,"className": "text-center"},
                    {"data": "actions", "orderable": false, "className": "text-center"}
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
    jQuery(document).ready(function () {
        table.init();

        $("#download_excel").click(function(event){
            var user_group =$("#user_group").val();
            var search = $('.dataTables_filter input').val();
            const searchData = document.getElementById('searchData').value;
            const searchName = $("#searchData").attr('data-name');                     

            window.location.href="<?php echo site_url('configuration/users/download_excel?') ?>user_group="+user_group+"&search="+search+"&searchData="+searchData+"&searchName="+searchName;
        });

        $("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });        

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);
    });

    changeSearch=(x,name)=>
    {
        $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#searchData").attr('data-name', name);

    }	

</script>
