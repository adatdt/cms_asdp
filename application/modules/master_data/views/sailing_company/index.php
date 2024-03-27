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

        <?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select id="port" class="form-control js-data-example-ajax select2" dir="" name="port">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($port as $key=>$value ) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Perusahaan</div>
                                                <select id="company" class="form-control js-data-example-ajax select2 input-medium" dir="" name="company">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($company as $key=>$value ) { ?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>    
                                        </div>

                                    </div>
                                </div>

                                <table class="table table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>NAMA PELAYARAN</th>
                                            <th>KODE PELAYARAN</th>
                                            <th>NAMA PERUSAHAAN</th>
                                            <th>PELABUHAN</th>
                                            <th>STATUS</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                 </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('master_data/sailing_company') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port = document.getElementById('port').value;
                    d.company = document.getElementById('company').value;
                },
            },
       
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "segment", "orderable": true, "className": "text-left"},
                    {"data": "segment_code", "orderable": true, "className": "text-left"},
                    {"data": "company_name", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
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

function get_dock()
{
    $.ajax({
        data:"port="+$("#port").val(),
        url:"<?php echo site_url()?>pelabuhan/dock/get_dock",
        type:"post",
        dataType:"json",
        success:function(x)
        {
            var html="<option value=''>Pilih</option>";

            for(var i=0;i<x.length;i++)
            {
                html +="<option value="+x[i].id+">"+x[i].name+"</option>";
            } 

            $("#dock").html(html);
            // console.log(html);
        }
    });
}
    
    jQuery(document).ready(function () {
        table.init();

        $("#port").on("change",function(){
            table.reload();
        });

        $("#dock").on("change",function(){
            table.reload();
        });

        $("#company").on("change",function(){
            table.reload();
        });        


        $("#port").on("change",function(){
            get_dock();
        });
        
    });
</script>
