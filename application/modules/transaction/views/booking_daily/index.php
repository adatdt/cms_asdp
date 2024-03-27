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
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
                </div>
            </div>
        </div>
        <?php $now = date("Y-m-d");
        $last_week = date('Y-m-d', strtotime("-1 days")) ?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title ?></div>
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
                                                <div class="input-group-addon">Tanggal Berangkat</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Pelabuhan</div>

                                                <?php echo form_dropdown("port",$port, '', 'id="port" class="form-control select2"'); ?>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Kelas</div>
                                                <?php echo form_dropdown('', $kelas, '', 'id="kelas" class="form-control select2"'); ?>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Shift</div>
                                                <?php echo form_dropdown('shift', $shift, '', 'id="shift" class="form-control select2"'); ?>
                                            </div>

                                            <div class="col-sm-12 form-inline" style="margin: 5px 0;"></div>
                                            <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Booking
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nama Penumpang/Driver','customerName')">Nama Penumpang/Driver</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nomor Identitas','nik')">Nomor Identitas</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('Nomor Plat','platNumber')">Nomor Plat</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="bookingCode" name="searchData" id="searchData"> 
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
                                <div class="kt-portlet__body" style="padding-top: 20px">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item active">
                                            <a id="tabPenumpang" class="label label-primary" data-toggle="tab" data-target="#penumpang" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Penumpang</a>
                                        </li>
                                        <li class="nav-item">
                                            <a id="tabKendaraan" class="label label-primary" data-toggle="tab" data-target="#kendaraan" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Kendaraan</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content ">
                                    <!-- tab data penumpang -->
                                    <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: 10px">
                                    <button id="excelkita" class="btn btn-sm btn-default download" style="display:none"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>
                                        <table class="table table-bordered table-striped table-hover" id="dataTables">
                                            <thead>
                                                <tr>
                                                    <th colspan="16" style="text-align: left">DATA PENUMPANG</th>
                                                </tr>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>TANGGAL BOOKING</th>
                                                    <th>TANGGAL BERANGKAT</th>
                                                    <th>NAMA PENUMPANG</th>
                                                    <th>NOMOR IDENTITAS</th>
                                                    <th>JENIS IDENTITAS</th>
                                                    <th>JENIS KELAMIN</th>
                                                    <th>TIPE PENUMPANG</th>
                                                    <th>KELAS</th>
                                                    <th>PELABUHAN</th>
                                                    <th>SERVIS</th>
                                                    <th>TARIF (Rp.)</th>
                                                    <th>SHIFT</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                    <!-- Data Kendaraan -->
                                    <div class="tab-pane " id="kendaraan" role="tabpanel" style="padding: 10px">
                                    <button id="excelkita1" class="btn btn-sm btn-default download1" style="display:none"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>    
                                    <table class="table table-bordered table-striped   table-hover" id="dataTables2">
                                            <thead>
                                                <tr>
                                                    <th colspan="16" style="text-align: left">DATA KENDARAAN</th>
                                                </tr>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>TANGGAL BOOKING</th>
                                                    <th>TANGGAL BERANGKAT</th>
                                                    <th>PLAT NOMOR</th>
                                                    <th>GOLONGAN</th>
                                                    <th>KELAS</th>
                                                    <th>DRIVER</th>
                                                    <th>PELABUHAN</th>
                                                    <th>BERAT KENDARAAN <br>(JEMBATAN TIMBANG)</th>
                                                    <th>TARIF (Rp.)</th>
                                                    <th>SHIFT</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "fileJs.php" ?>
<script type="text/javascript">
    var initDT = false;
    var target = '#penumpang';
    myData = new MyData();

    jQuery(document).ready(function() {
        myData.init();
        myData.init2();
        $("#excelkita").show();
        $("#excelkita1").show();
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });
        $("#excelkita").on("click", function(e) {
            port = $("#port").val();
            kelas = $("#kelas").val();
            dateFrom = $("#dateFrom").val();
            dateTo = $("#dateTo").val();
            cari = $("#searchData").val();
            searchName=$("#searchData").attr('data-name');

            if (target == '#kendaraan') {
                window.location.href = "<?php echo site_url('transaction/booking_daily/excel_kendaraan?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&port=" + port + "&kelas=" + kelas + "&cari=" + cari + "&searchName=" + searchName;
            } else {
                window.location.href = "<?php echo site_url('transaction/booking_daily/excel_penumpang?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&port=" + port + "&kelas=" + kelas + "&cari=" + cari + "&searchName=" + searchName;;
            }
        });
        $("#excelkita1").on("click", function(e) {
            port = $("#port").val();
            kelas = $("#kelas").val();
            shift = $("#shift").val();
            dateFrom = $("#dateFrom").val();
            dateTo = $("#dateTo").val();
            cari = $("#searchData").val();
            searchName=$("#searchData").attr('data-name');

            if (target == '#kendaraan') {
                window.location.href = "<?php echo site_url('transaction/booking_daily/excel_kendaraan?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&port=" + port + "&kelas=" + kelas+"&shift="+shift + "&cari=" + cari + "&searchName=" + searchName;;
            } else {
                window.location.href = "<?php echo site_url('transaction/booking_daily/excel_penumpang?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&port=" + port + "&kelas=" + kelas+"&shift="+shift + "&cari=" + cari + "&searchName=" + searchName;;
            }
        });
        // $("#dateFrom").change(function() {
        //     table_kendaraan.reload();
        //     table_penumpang.reload();
        //     $("#excelkita").show();
        // });
        // $("#dateTo").change(function() {
        //     table_kendaraan.reload();
        //     table_penumpang.reload();
        //     $("#excelkita").show();
        // });
        // $("#port").change(function() {
        //     table_kendaraan.reload();
        //     table_penumpang.reload();
        //     $("#excelkita").show();
        // });

        // $("#shift").change(function() {
        //     table_kendaraan.reload();
        //     table_penumpang.reload();
        //     $("#excelkita").show();
        // });
        

        // $("#kelas").change(function() {
        //     table_kendaraan.reload();
        //     table_penumpang.reload();
        //     $("#excelkita").show();
        // });
        $("#cari").on("click",function(e){
			$(this).button('loading');
            $("#tabPenumpang").button('loading');
            $("#tabKendaraan").button('loading');
			e.preventDefault();
            $("#excelkita").show();
            myData.reload('dataTables');
            myData.reload('dataTables2');
            $('#dataTables , dataTables2 ').on('draw.dt', function() {
                $("#cari").button('reset');
            });
		});
        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);
    });
    $(document).on('click', '[data-toggle="tab"]', function() {
        target = $(this).data('target');
    });
</script>