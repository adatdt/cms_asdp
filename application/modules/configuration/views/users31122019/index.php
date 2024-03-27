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
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
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

                                    </div>

                                </div>
                            </div>
                        <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Depan</th>
                                <th>Nama Belakang</th>
                                <th>Username</th>
                                <th>Group</th>
                                <th>Pelabuhan</th>
                                <th>Login CMS</th>
                                <th>Login Validator</th>
                                <th>Login EKTP Reader</th>
                                <th>Login CS</th>
                                <th>Login POS </th>
                                <th>Status </th>
                                <th class="text-center">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                Aksi
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
var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('configuration/users/ajaxlist_user') ?>",
                "type": "POST",
                "data": function(d) {
                     d.user_group =document.getElementById('user_group').value;
                },
            },


         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center", "width": 20},
                    {"data": "first_name", "orderable": true},
                    {"data": "last_name", "orderable": true},
                    {"data": "username", "orderable": true},
                    {"data": "group_name", "orderable": true},
                    {"data": "port_name", "orderable": true},
                    {"data": "admin_pannel_login", "orderable": true ,"className": "text-center"},
                    {"data": "validator_login", "orderable": true ,"className": "text-center"},
                    {"data": "e_ktp_reader_login", "orderable": true ,"className": "text-center"},
                    {"data": "cs_login", "orderable": true,"className": "text-center"},
                    {"data": "pos_login", "orderable": true,"className": "text-center"},
                    {"data": "status", "orderable": true,"className": "text-center"},
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

        $("#user_group").on("change",function(){
           table.reload(); 
        });
    });
</script>
