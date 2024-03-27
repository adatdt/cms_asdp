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
                    <?php echo '<a href="' . $url_parent1 . '">' . $parent1; ?></a>
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

        <br>
        <!-- start: Gate In: Summary -->
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $title; ?>
                </div>
                <div class="pull-right btn-add-padding">
                    <a href="<?php echo site_url('open_boarding') ?>" class="btn btn-sm btn-warning">Kembali</a>
                </div>
            </div>
            <div class="portlet-body">
            
                <!-- start -->
                <ul class="nav nav-tabs">
                  <li class="<?php echo ($tab == 'passanger') ? 'active' : ''; ?>"><a href="#passanger" data-toggle="tab"><span style="font-size:13px width: 50px text-align: center" class="widget-caption btn ">Boarding Penumpang</span></a></li>
                 <li class="<?php echo ($tab == 'passanger_vehicle') ? 'active' : ''; ?>"><a href="#passanger_vehicle" data-toggle="tab"><span style="font-size:13px width: 50px text-align: center" class="widget-caption btn ">Boarding Penumpang Kendaraan</span></a></li>
                  <li class="<?php echo ($tab == 'vehicle') ? 'active' : ''; ?>"><a href="#vehicle" data-toggle="tab"><span style="font-size:13px width: 140px text-align: center" class="widget-caption btn ">Boarding Kendaraan </span></a></li>

                  
                </ul>
                <!-- end -->
                <div class="tab-content">
                <div class="tab-pane <?php echo ($tab == 'passanger') ? 'active' : ''; ?>" id="passanger">
                <!--
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Boarding</div>
                        <input class="form-control input-small boardingDate" id="boardingDate1" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Keberangakatan</div>
                        <input class="form-control input-small boardingDate" id="departDate1" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <button type="button " class="btn btn-info" id="cari_tanggal1" >cari</button>
                </div>
                <br />
                <div align="right">
                Cari : <input type="input" name="caridata1" id="caridata1" />
                </div>             
                -->    
                    <span id='export_tools'> <a href="javascript:;" data-action="0" class="tool-action btn btn-warning" id="export_tools"><i class="icon-doc"></i> Export</a></span> 
                <br />
                <br />
                <table class="table table-bordered table-hover" id="tbltable1">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>No Ticket</th>
                            <th>Jenis Identitas</th>
                            <th>No Identitas</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Umur</th>
                            <th>alamat</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($manifestpassanger as $manifestpassanger) { ?>
                        <tr>
                         <td>1</td>
                         <td><?php echo $manifestpassanger->ticket_number; ?></td>
                         <td><?php echo empty($manifestpassanger->id_type)?'lainya':$manifestpassanger->id_type; ?></td>  
                         <td><?php echo $manifestpassanger->id_number; ?></td> 
                         <td><?php echo $manifestpassanger->name; ?></td>
                          <td><?php echo $manifestpassanger->gender; ?></td>
                         <td><?php echo $manifestpassanger->age; ?></td> 
                         <td><?php echo $manifestpassanger->city; ?></td> 
                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot></tfoot>
                </table>
                </div>
                
                <div class="tab-pane <?php echo ($tab == 'passanger_vehicle') ? 'active' : ''; ?>" id="passanger_vehicle">
                <!--
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Boarding</div>
                        <input class="form-control input-small boardingDate" id="boardingDate3" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Keberangakatan</div>
                        <input class="form-control input-small boardingDate" id="departDate3" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <button type="button " class="btn btn-info" id="cari_tanggal3" >cari</button>
                </div>
                <br />
                <div align="right">
                Cari : <input type="input" name="caridata3" id="caridata3" />
                </div> 
                --> 
                <span id='export_tools3'> <a href="javascript:;" data-action="0" class="tool-action btn btn-warning" id="export_tools"><i class="icon-doc"></i> Export</a></span>
                <br>                                     
                <br />
                <table class="table table-bordered table-hover" id='tbltable3'>
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>No Ticket</th>
                            <th>No identitas</th>
                            <th>Jenis Identitas</th>
                            <th>No Polisi</th>
                            <th>Golongan</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Umur</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach ($passangervehicle as $passangervehicle) { ?>
                        <tr>
                         <td><?php echo $no; ?></td>
                         <td><?php echo $passangervehicle->ticket; ?></td>
                         <td><?php echo $passangervehicle->id_number; ?></td>
                         <td><?php echo empty($passangervehicle->id_type)?'lainya':$passangervehicle->id_type; ?></td>  
                         <td><?php echo $passangervehicle->no_pol; ?></td> 
                         <td><?php echo $passangervehicle->vehicle_name; ?></td>  
                         <td><?php echo $passangervehicle->name; ?></td>
                          <td><?php echo $passangervehicle->gender; ?></td>
                         <td><?php echo $passangervehicle->age; ?></td> 
                         <td><?php echo $passangervehicle->city; ?></td> 
                        </tr>
                    <?php $no++; } ?>
                    </tbody>
                    <tfoot></tfoot>
                </table>
                </div>
                
                <div class="tab-pane <?php echo ($tab == 'vehicle') ? 'active' : ''; ?>" id="vehicle">
                <span id='export_tools2'> <a href="javascript:;" data-action="0" class="tool-action btn btn-warning" id="export_tools"><i class="icon-doc"></i> Export</a></span> 
                <br >
                <br>
                <table class="table table-bordered table-hover" id="tbltable2">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>No Tiket</th>
                            <th>NO Polisi</th>
                            <th>Golongan</th>
                            <th>Jumlah Penumpang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach ($vehicle as $vehicle) 
                            
                        { 
                            $tot=$this->db->query("select *  from app.t_trx_booking_passanger where booking_id=".$vehicle->booking_id."")->num_rows();
                            
                        ?>
                        <tr>
                         <td><?php echo $no; ?></td>
                         <td><?php echo $vehicle->ticket; ?></td>
                         <td><?php echo $vehicle->id_number; ?></td>
                         <td><?php echo $vehicle->vehicle_name; ?></td> 
                         <td><?php echo $tot; ?></td>  
 
                        </tr>
                    <?php $no++; } ?>
                    </tbody>
                    <tfoot></tfoot>
                </table>
                </div>
                </div>
            </div>
        </div>
        <!-- end: Gate In: Summary -->
        
    </div>
</div>
<script type="text/javascript">

var table1= {

    loadData: function() {
        $('#tbltable1').DataTable({
        
           
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",

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
            "searching":false,
            "lengthMenu": [
                [10, 25, 50, 100,-1],
                [10, 25, 50, 100,"all"]
            ],
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
         //   "order": [[3, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tbltable1').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },  
            buttons: [{extend: 'excel'}]
        });

        $('#export_tools  > a.tool-action').on('click', function() {
            var data_tables = $('#tbltable1').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    },

    reload: function() {
        $('#tbltable1').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};


var table2= {

    loadData: function() {
        $('#tbltable2').DataTable({
        
           
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",

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
            "searching":false,
            "lengthMenu": [
                [10, 25, 50, 100,-1],
                [10, 25, 50, 100,"all"]
            ],
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
         //   "order": [[3, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tbltable2').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },  
            buttons: [
                {
                    extend: 'excel',
                    },
            ]
        });

        $('#export_tools2  > a.tool-action').on('click', function() {
            var data_tables = $('#tbltable2').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    },

    reload: function() {
        $('#tbltable2').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};


var table3= {

    loadData: function() {
        $('#tbltable3').DataTable({
        
           
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",

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
            "searching":false,
            "lengthMenu": [
                [10, 25, 50, 100,-1],
                [10, 25, 50, 100,"all"]
            ],
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
         //   "order": [[3, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tbltable3').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },  
            buttons: [
                {
                    extend: 'excel',
                    },
            ]
        });

        $('#export_tools3  > a.tool-action').on('click', function() {
            var data_tables = $('#tbltable3').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    },

    reload: function() {
        $('#tbltable3').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

jQuery(document).ready(function () {
    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);

table1.init();
table2.init();
table3.init();

    $('.boardingDate').datepicker({
        format: 'yyyy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        todayHighlight: true,
    }).on('changeDate',function(e) {});
    
    
    $("#caridata1").on("keypress",function(){
        if(event.which == 13){
            boarding.reload();
        }
    });
    
    $("#caridata3").on("keypress",function(){
        if(event.which == 13){
            boarding3.reload();
        }
    });
    
    $("#caridata2").on("keypress",function(){
        if(event.which == 13){
            boarding2.reload();
        }
    });
    
    $("#cari_tanggal1").on("click", function (){
        boarding.reload();
    });
    
    $("#cari_tanggal2").on("click", function (){
        boarding2.reload();
    });
    
    $("#cari_tanggal3").on("click", function (){
        boarding3.reload();
    });

$("#tblboarding").dataTable({

            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",

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
            "searching":false,
            "lengthMenu": [
                [10, 25, 50, 100,-1],
                [10, 25, 50, 100,"all"]
            ],
            "pageLength": -1,

}
);
$("#boarding_passanger_vehicle").dataTable({
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",

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
            "searching":false,
            "lengthMenu": [
                [10, 25, 50, 100,-1],
                [10, 25, 50, 100,"all"]
            ], 
            "pageLength": -1,   
});

$("#tblboarding2").dataTable({

            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",

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
            "searching":false,
            "lengthMenu": [
                [10, 25, 50, 100,-1],
                [10, 25, 50, 100,"all"]
            ],
            "pageLength": -1,

});

});
</script>