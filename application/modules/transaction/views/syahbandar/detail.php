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
                    <!-- <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div> -->
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
                                                <div class="input-group-addon">Tanggal Booking</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>">
<!--                                                 <div class="input-group-addon "><i class="icon-calendar"></i></div> -->

                                            </div>    

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Servis</div>
                                                <select id="service" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($service as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>    


                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tujuan</div>
                                                <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div> 

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Keberangkatan</div>
                                                <select id="port_destination" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_destination">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>       

                                        </div>

                                    </div>
                                </div>


                                <table class="table table-bordered table-hover" id="dataTables">
                                <thead>
                                <tr>
                                    <th colspan="16">DATA PENUMPANG</th>
                                </tr>
                                <tr>
                                    <th>NO</th>
                                    <th>NOMER BOOKING</th>
                                    <th>NOMER TIKET</th>
                                    <th>NAMA</th>
                                    <th>Alamat</th>
                                    <th>NO</th>
                                    <th>NOMER BOOKING</th>
                                    <th>NOMER TIKET</th>
                                    <th>NAMA</th>
                                    <th>Alamat</th>
                                    <th>NO</th>
                                    <th>NOMER BOOKING</th>
                                    <th>NOMER TIKET</th>
                                    <th>NAMA</th>
                                    <th>Alamat</th>
                                    <th>NO</th>

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
                "url": "<?php echo site_url('transaction/booking/listDetail') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.service = document.getElementById('service').value;
                    d.port_origin = document.getElementById('port_origin').value;
                    d.port_destination = document.getElementById('port_destination').value;
                },
            },


         
            "serverSide": true,
            "processing": true,
            "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "booking_code", "orderable": true, "className": "text-left"},
                        {"data": "ticket_number", "orderable": true, "className": "text-left"},
                        {"data": "name", "orderable": true, "className": "text-left"},
                        {"data": "city", "orderable": true, "className": "text-left"},
                        {"data": "gender", "orderable": true, "className": "text-left"},
                        {"data": "age", "orderable": true, "className": "text-left"},
                        {"data": "birth_date", "orderable": true, "className": "text-left"},
                        {"data": "service_name", "orderable": true, "className": "text-left"},
                        {"data": "special_service_name", "orderable": true, "className": "text-left"},
                        {"data": "passenger_type_name", "orderable": true, "className": "text-left"},
                        {"data": "shift_class_name", "orderable": true, "className": "text-left"},
                        {"data": "origin_name", "orderable": true, "className": "text-left"},
                        {"data": "destination_name", "orderable": true, "className": "text-left"},
                        {"data": "fare", "orderable": true, "className": "text-left"},
                        {"data": "depart_time", "orderable": true, "className": "text-left"},
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

        // $('#export_tools > li > a.tool-action').on('click', function() {
        //     var data_tables = $('#dataTables').DataTable();
        //     var action = $(this).attr('data-action');

        //     data_tables.button(action).trigger();
        // });
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

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $("#dateTo").change(function(){
            table.reload();
        });

        $("#dateFrom").change(function(){
            table.reload();
        });

        $("#service").change(function(){
            table.reload();
        });

        $("#port_origin").change(function(){
            table.reload();
        });

        $("#port_destination").change(function(){
            table.reload();
        });
        
    });
</script>
