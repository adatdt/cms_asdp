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

                    <div class="caption"><?php echo $title; ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                <div class="table-toolbar">
                        <div class="row">
                            <!-- <div class="col-sm-12 form-inline">


                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Metode Pembayaran</div>
                                    <select id="method" class="form-control js-data-example-ajax select2 input-small" dir="" name="method">
                                        <option value="">Pilih</option>
                                        <?php foreach($method as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div> 

                            </div> -->

                            <div class="col-sm-12 form-inline">
                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="pelabuhan" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                        <?php if ($row_port != 0) {
                                        } else { ?>
                                            <option value="">Pilih</option>
                                        <?php }
                                        foreach ($port as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tipe Kapal</div>
                                    <select id="tipe_kapal" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
                                        <option value="">Pilih</option>
                                        <?php foreach ($ship_class as $key => $value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Status</div>
                                    <select id="status" class="form-control js-data-example-ajax select2 input-small" dir="" name="method">
                                        <option value="">Pilih</option>                                        
                                        <option value="<?php echo $this->enc->encode(1); ?>">AKTIF</option>
                                        <option value="<?php echo $this->enc->encode(0); ?>">TIDAK AKTIF</option>
                                    </select>
                                </div>

                               <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Batch
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Kode Batch','batchCode')">Kode Batch</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Nama Batch','batchName')">Nama Batch</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="batchCode" name="searchData" id="searchData"> 
                                </div>   
                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>KODE BATCH</th>
                                <th>NAMA BATCH</th>
                                <th>PELABUHAN</th>
                                <th>TIPE KAPAL</th>
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

<script type="text/javascript">

class myData {
    loadData() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('master_data/batch') ?>",
                "type": "POST",
                "data": function(d) {
                    d.pelabuhan = document.getElementById('pelabuhan').value;
                    d.tipe_kapal = document.getElementById('tipe_kapal').value;
                    d.status = document.getElementById('status').value;
                    d.searchName=$("#searchData").attr('data-name');
                    d.searchData=document.getElementById('searchData').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "batch_code", "orderable": true, "className": "text-left"},
                    {"data": "batch_name", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "ship_class_name", "orderable": true, "className": "text-left"},
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
            "searching" : false,
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

    changeSearch(x,name)
    {
        $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#searchData").attr('data-name', name);

    }
};

    var table = new myData();
    jQuery(document).ready(function () {
        table.init();

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });
    });

</script>
